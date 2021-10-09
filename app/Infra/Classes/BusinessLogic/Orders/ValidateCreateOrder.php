<?php

namespace App\Infra\Classes\BusinessLogic\Orders;

use App\Infra\Classes\BusinessLogic\Reservations\ReservationStatuses;
use App\Infra\Classes\Common\Errors;
use App\Infra\Interfaces\Repositories\ItemsRepositoryInterface;
use App\Infra\Interfaces\Repositories\ReservationsRepositoryInterface;
use App\Infra\Interfaces\Validators\ValidatorInterface;
use App\Infra\Traits\TriggerErrorsTrait;
use App\Models\Item;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ValidateCreateOrder
{
    use TriggerErrorsTrait;

    private ValidatorInterface $validator;
    private ItemsRepositoryInterface $itemsRepository;
    private ReservationsRepositoryInterface $reservationsRepository;
    private Collection $itemsWithValidPromoCodes;

    public function __construct(ValidatorInterface $validator, ItemsRepositoryInterface $itemsRepository
        , ReservationsRepositoryInterface          $reservationsRepository)
    {
        $this->validator = $validator;
        $this->itemsRepository = $itemsRepository;
        $this->reservationsRepository = $reservationsRepository;
    }

    private function setItemsWithValidPromoCodes()
    {
        $promoCodes = array_values(
            array_unique(
                Arr::flatten(
                    array_column(
                        $this->validator->getResource()->get('items'), 'promo_codes')), SORT_REGULAR));
        $this->itemsWithValidPromoCodes = $this->itemsRepository
            ->whereIn('id', array_column($this->validator->getResource()->get('items'), 'id'))
            ->where('qty', 0, '>')
            ->withValidPromoCodes(['id', 'reward', 'type'], $promoCodes)
            ->select(['qty', 'price'])
            ->get();
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
        if (!$reservation) {
            $this->trigger(Errors::RESERVATION_UNAVAILABLE);
            return false;
        }
        return true;
    }

    private function validateItems(): bool
    {
        $valid = false;
        $this->setItemsWithValidPromoCodes();
        if (isset($this->itemsWithValidPromoCodes)) {
            $valid =
                count($this->itemsWithValidPromoCodes) ===
                count(array_unique(array_column($this->validator->getResource()->get('items'), 'id'), SORT_REGULAR));
            if (!$valid) {
                $this->trigger(Errors::INVALID_OR_OUT_OF_STOCK_ITEM);
            }
        }
        return $valid;
    }

    private function validatePromoCodes(array $itemRequest, Item $relatedItem)
    {
        if (array_key_exists('promo_codes', $itemRequest)) {
            if (count(
                    $itemRequest['promo_codes']) !==
                count(array_unique($itemRequest['promo_codes']), SORT_REGULAR)) {
                $this->addErrorToField(Errors::DUPLICATED_PROMO_CODE, $itemRequest['id']);
            }
            $invalidPromoCodes = (array_diff(
                $itemRequest['promo_codes'],
                $relatedItem->validPromoCodes->pluck('id')->toArray()
            ));
            if ($invalidPromoCodes) {
                $this->errors[Errors::INVALID_PROMO_CODE][$itemRequest['id']] = array_values($invalidPromoCodes);
            }
        }
    }

    private function validateQuantity(Item $relatedItem): void
    {
        $requiredQty = 0;
        foreach ($this->validator->getResource()->get('items') as $itemRequest) {
            if ($itemRequest['id'] === $relatedItem->id) {
                $requiredQty += $itemRequest['qty'];
            }
        }
        if ($requiredQty > $relatedItem['qty']) {
            $this->addErrorToField(Errors::INSUFFICIENT_QUANTITY, $relatedItem->id);
        }
    }

    private function validateQuantityAndPromoCodes(): bool
    {
        foreach ($this->validator->getResource()->get('items') as $itemRequest) {
            $relatedItem = $this->itemsWithValidPromoCodes->where('id', $itemRequest['id'])->first();
            $this->validatePromoCodes($itemRequest, $relatedItem);
            $this->validateQuantity($relatedItem);
        }
        return !count($this->errors);
    }

    public function validate(): bool
    {
        return $this->validRequestStructure()
            && $this->validateReservation()
            && $this->validateItems()
            && $this->validateQuantityAndPromoCodes();
    }

    public function getErrors(): array
    {
        return array_merge($this->errors, $this->validator->getErrors());
    }

    public function getRequestItems(): array
    {
        return $this->validator->getResource()->get('items');
    }

    public function getRelatedItems(): Collection
    {
        return $this->itemsWithValidPromoCodes;
    }

    public function getReservationId(): int
    {
        return $this->validator->getResource()->get('reservation_id');
    }
}
