<?php

namespace App\Infra\Classes\BusinessLogic\Orders;

use App\Infra\Classes\BusinessLogic\Reservations\ReservationStatuses;
use App\Infra\Classes\Common\Errors;
use App\Infra\Interfaces\Repositories\OrdersRepositoryInterface;
use App\Infra\Interfaces\Repositories\ReservationsRepositoryInterface;
use App\Infra\Interfaces\Validators\ValidatorInterface;
use App\Infra\Traits\TriggerErrorsTrait;
use App\Models\Order;

class CheckoutOrder
{
    use TriggerErrorsTrait;

    private ValidatorInterface $validator;
    private OrdersRepositoryInterface $ordersRepository;
    private ReservationsRepositoryInterface $reservationsRepository;
    private Order $order;

    public function __construct(ValidatorInterface              $validator, OrdersRepositoryInterface $ordersRepository,
                                ReservationsRepositoryInterface $reservationsRepository)
    {
        $this->validator = $validator;
        $this->ordersRepository = $ordersRepository;
        $this->reservationsRepository = $reservationsRepository;
    }

    public function validate(): bool
    {
        $this->validator->string(['order_id']);
        if ($this->validator->validate()) {
            $order = $this->ordersRepository
                ->readById($this->validator->getResource()->get('order_id'))
                ->where('paid', false)
                ->withReservation(['customer_id'])
                ->withItems(['name'])
                ->withPromoCodes(['reward', 'type'])
                ->first();
            if ($order) {
                $this->order = $order;
                return true;
            } else {
                $this->trigger(Errors::INVALID_ORDER);
            }
        }
        return false;
    }

    private function markAsPaid()
    {
        $this->order->update([
            'paid' => true
        ]);
    }

    private function checkReservationStatus()
    {
        $notPaidReservations = $this->ordersRepository
            ->reset()
            ->where('reservation_id', $this->order->reservation_id)
            ->where('paid', false)
            ->count();
        if (!$notPaidReservations) {
            $this->reservationsRepository
                ->readById($this->order->reservation_id)
                ->update([
                    'status' => ReservationStatuses::RESERVATION_SETTLED
                ]);
        }
    }

    public function getDetails(): array
    {
        $this->markAsPaid();
        $this->checkReservationStatus();
        return $this->order->toArray();
    }

    public function getErrors(): array
    {
        return array_merge($this->errors, $this->validator->getErrors());
    }
}
