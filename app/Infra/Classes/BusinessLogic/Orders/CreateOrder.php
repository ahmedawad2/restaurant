<?php

namespace App\Infra\Classes\BusinessLogic\Orders;

use App\Infra\Classes\BusinessLogic\Reservations\ReservationStatuses;
use App\Infra\Classes\Common\Errors;
use App\Infra\Interfaces\Repositories\ItemsOrdersPromoCodesRepositoryInterface;
use App\Infra\Interfaces\Repositories\ItemsRepositoryInterface;
use App\Infra\Interfaces\Repositories\OrdersRepositoryInterface;
use App\Infra\Interfaces\Repositories\ReservationsRepositoryInterface;
use App\Infra\Interfaces\Validators\ValidatorInterface;
use App\Infra\Traits\TriggerErrorsTrait;
use App\Models\Item;
use App\Models\Order;
use App\Models\Reservation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateOrder
{
    use TriggerErrorsTrait;

    private ValidatorInterface $validator;
    private ItemsRepositoryInterface $itemsRepository;
    private ReservationsRepositoryInterface $reservationsRepository;
    private OrdersRepositoryInterface $ordersRepository;
    private ItemsOrdersPromoCodesRepositoryInterface $itemsOrdersPromoCodesRepository;
    private Reservation $reservation;
    private Order $order;
    private Collection $itemsWithValidPromoCodes;
    private array $itemIds;
    private float $total = 0;
    private float $subTotal = 0;

    public function __construct(ValidatorInterface                       $validator, ItemsRepositoryInterface $itemsRepository,
                                ReservationsRepositoryInterface          $reservationsRepository, OrdersRepositoryInterface $ordersRepository,
                                ItemsOrdersPromoCodesRepositoryInterface $itemsOrdersPromoCodesRepository)
    {
        $this->validator = $validator;
        $this->itemsRepository = $itemsRepository;
        $this->reservationsRepository = $reservationsRepository;
        $this->ordersRepository = $ordersRepository;
        $this->itemsOrdersPromoCodesRepository = $itemsOrdersPromoCodesRepository;
    }

    private function validRequestStructure(): bool
    {
        $this->validator->integer(['reservation_id']);
        $this->validator->keysInArray([0], 'items');
        for ($i = 0; $i < count($this->validator->getResource()->get('items')); $i++) {
            $this->validator->integer(['items.' . $i . '.id'])
                ->numeric(['items.' . $i . '.qty']);
            if ($this->validator->has('items.' . $i . '.promo_codes')) {
                $this->validator->keysInArray([0], 'items.' . $i . '.promo_codes');
                if ($this->validator->validate()) {
                    for ($j = 0; $j < count($this->validator->getResource()->get('items')[$i]['promo_codes']); $j++) {
                        $this->validator->string(['items.' . $i . '.promo_codes.' . $j]);
                    }
                }
            }
        }
        return $this->validator->validate();
    }

    private function validateReservation(): bool
    {
        $reservation = $this->reservationsRepository
            ->where('customer_id', Auth::guard('customers')->id())
            ->where('status', ReservationStatuses::RESERVATION_ACTIVE)
            ->where('id', $this->validator->getResource()->get('reservation_id'))
            ->select(['customer_id', 'table_id'])
            ->first();
        if ($reservation instanceof Reservation) {
            $this->reservation = $reservation;
        } else {
            $this->trigger(Errors::RESERVATION_UNAVAILABLE);
        }
        return isset($this->reservation);
    }

    private function validateItems(): bool
    {
        $valid = false;
        $this->setItemIds();
        $this->setItemsWithValidPromoCodes();
        if (isset($this->itemsWithValidPromoCodes)) {
            $valid = count($this->itemsWithValidPromoCodes) === count(array_unique($this->itemIds, SORT_REGULAR));
            if (!$valid) {
                $this->trigger(Errors::INVALID_OR_OUT_OF_STOCK_ITEM);
            }
        }
        return $valid;
    }

    private function setItemIds()
    {
        $this->itemIds = array_column($this->validator->getResource()->get('items'), 'id');
    }

    private function setItemsWithValidPromoCodes()
    {
        $promoCodes = array_values(
            array_unique(
                Arr::flatten(
                    array_column(
                        $this->validator->getResource()->get('items'), 'promo_codes')), SORT_REGULAR));
        $this->itemsWithValidPromoCodes = $this->itemsRepository
            ->whereIn('id', $this->itemIds)
            ->where('qty', 0, '>')
            ->withValidPromoCodes(['id', 'reward', 'type'], $promoCodes)
            ->select(['qty', 'price'])
            ->get();
    }

    private function processTotalFor(array $itemRequest, Item $relatedItem)
    {
        $this->total +=
            $itemRequest['qty'] * $relatedItem->price;
    }

    private function processSubTotalFor(array $itemRequest, Item $relatedItem)
    {
        $price = $relatedItem->price;
        if (array_key_exists('promo_codes', $itemRequest)) {
            foreach ($itemRequest['promo_codes'] as $promoCodeId) {
                $relatedPromoCode = $relatedItem->validPromoCodes->where('id', $promoCodeId)->first();
                switch ($relatedPromoCode->type) {
                    case 1:
                        $price -= $relatedPromoCode->reward;
                        break;
                    case 2:
                        $price -= ($relatedPromoCode->reward * $price / 100);
                        break;
                }
            }
            $price = $price > 0
                ? $price
                : 0;
        }

        $this->subTotal += $price * $itemRequest['qty'];
    }

    public function validatePromoCodes(): bool
    {
        foreach ($this->validator->getResource()->get('items') as $itemWithPromoCodes) {
            $relatedItem = $this->itemsWithValidPromoCodes->where('id', $itemWithPromoCodes['id'])->first();
            if (array_key_exists('promo_codes', $itemWithPromoCodes)) {
                if (count($itemWithPromoCodes['promo_codes']) !== count(array_unique($itemWithPromoCodes['promo_codes']), SORT_REGULAR)) {
                    $this->addErrorToField(Errors::DUPLICATED_PROMO_CODE, $itemWithPromoCodes['id']);
                }
                $invalidPromoCodes = (array_diff(
                    $itemWithPromoCodes['promo_codes'],
                    $relatedItem->validPromoCodes->pluck('id')->toArray()
                ));
                if ($invalidPromoCodes) {
                    $this->errors[Errors::INVALID_PROMO_CODE][$itemWithPromoCodes['id']] = array_values($invalidPromoCodes);
                }
            }
        }
        return !count($this->errors);
    }

    public function calculate()
    {
        foreach ($this->validator->getResource()->get('items') as $itemWithPromoCodes) {
            $relatedItem = $this->itemsWithValidPromoCodes->where('id', $itemWithPromoCodes['id'])->first();
            $this->processTotalFor($itemWithPromoCodes, $relatedItem);
            $this->processSubTotalFor($itemWithPromoCodes, $relatedItem);
        }
    }

    public function validRequest(): bool
    {
        return $this->validRequestStructure()
            && $this->validateReservation()
            && $this->validateItems()
            && $this->validatePromoCodes();
    }

    public function getErrors(): array
    {
        return array_merge($this->errors, $this->validator->getErrors());
    }

    public function getTotal(): float
    {
        return round($this->total, 2);
    }

    public function getSubTotal(): float
    {
        return round($this->subTotal, 2);
    }

    public function create(): bool
    {
        DB::beginTransaction();
        try {
            $this->order = $this->ordersRepository->create([
                'reservation_id' => $this->validator->getResource()->get('reservation_id'),
                'total' => $this->total,
                'sub_total' => $this->subTotal,
            ]);
            $attachedItemsWithPromoCodes = [];
            foreach ($this->validator->getResource()->get('items') as $itemRequest) {
                $relatedItem = $this->itemsWithValidPromoCodes->where('id', $itemRequest['id'])->first();
                $toBeAttached = [
                    'order_id' => $this->order->id,
                    'item_id' => $relatedItem->id,
                    'price' => $relatedItem->price,
                    'qty' => $itemRequest['qty'],
                    'promo_codes' => array_key_exists('promo_codes', $itemRequest)
                        ? json_encode($itemRequest['promo_codes'])
                        : null
                ];
                $attachedItemsWithPromoCodes[] = $toBeAttached;
            }
            $this->itemsOrdersPromoCodesRepository->insert($attachedItemsWithPromoCodes);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
