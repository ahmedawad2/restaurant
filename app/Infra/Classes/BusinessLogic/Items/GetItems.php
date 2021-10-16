<?php

namespace App\Infra\Classes\BusinessLogic\Items;

use App\Infra\Classes\Common\Constants;
use App\Infra\Classes\Common\Errors;
use App\Infra\Interfaces\Repositories\ItemsRepositoryInterface;
use App\Infra\Interfaces\Repositories\ReservationsRepositoryInterface;
use App\Infra\Interfaces\Validators\ValidatorInterface;
use App\Infra\Traits\TriggerErrorsTrait;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class GetItems
{
    use TriggerErrorsTrait;

    private ValidatorInterface $validator;
    private ItemsRepositoryInterface $itemsRepository;
    private ReservationsRepositoryInterface $reservationsRepository;
    private Reservation $reservation;

    public function __construct(ValidatorInterface              $validator, ItemsRepositoryInterface $itemsRepository,
                                ReservationsRepositoryInterface $reservationsRepository)
    {
        $this->validator = $validator;
        $this->itemsRepository = $itemsRepository;
        $this->reservationsRepository = $reservationsRepository;
    }

    private function validateReservation(): bool
    {
        if ($this->validator->sent('reservation_id') && $this->validator->integer(['reservation_id'])->validate()) {
            $this->setReservation();
            $valid = isset($this->reservation);
            if (!$valid) {
                $this->trigger(Errors::RESERVATION_UNAVAILABLE);
            }
            return $valid;
        }
        return true;
    }

    private function setReservation()
    {
        $reservation = $this->reservationsRepository
            ->where('id', $this->validator->getResource()->get('reservation_id'))
            ->where('customer_id', Auth::guard('customers')->id())
            ->select(['from', 'to'])
            ->first();
        if ($reservation instanceof Reservation) {
            $this->reservation = $reservation;
        }
    }

    public function validRequest(): bool
    {
        return $this->validateReservation();
    }

    public function getItems()
    {
        $day = $from = $to = null;
        if (isset($this->reservation)) {
            $from = $this->reservation->from->format(Constants::DEFAULT_TIME_FORMAT);
            $to = $this->reservation->to->format(Constants::DEFAULT_TIME_FORMAT);
            $day = $this->reservation->from->dayOfWeek;
        }
        return $this->itemsRepository->available($day, $from, $to)
            ->select(['name', 'price'])
            ->get();
    }

    public function getErrors(): array
    {
        return array_merge($this->errors, $this->validator->getErrors());
    }
}
