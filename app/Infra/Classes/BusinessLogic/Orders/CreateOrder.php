<?php

namespace App\Infra\Classes\BusinessLogic\Orders;

use App\Infra\Interfaces\Repositories\ItemsOrdersPromoCodesRepositoryInterface;
use App\Infra\Interfaces\Repositories\OrdersRepositoryInterface;
use App\Infra\Traits\TriggerErrorsTrait;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CreateOrder
{
    use TriggerErrorsTrait;

    private OrdersRepositoryInterface $ordersRepository;
    private ItemsOrdersPromoCodesRepositoryInterface $itemsOrdersPromoCodesRepository;
    private ValidateCreateOrder $validateCreateOrder;
    private Order $order;
    private float $total = 0;
    private float $subTotal = 0;

    public function __construct(OrdersRepositoryInterface $ordersRepository, ItemsOrdersPromoCodesRepositoryInterface $itemsOrdersPromoCodesRepository,
                                ValidateCreateOrder       $validateCreateOrder)
    {
        $this->validateCreateOrder = $validateCreateOrder;
        $this->ordersRepository = $ordersRepository;
        $this->itemsOrdersPromoCodesRepository = $itemsOrdersPromoCodesRepository;
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

    private function calculate()
    {
        foreach ($this->validateCreateOrder->getRequestItems() as $itemWithPromoCodes) {
            $relatedItem = $this->validateCreateOrder->getRelatedItems()->where('id', $itemWithPromoCodes['id'])->first();
            $this->processTotalFor($itemWithPromoCodes, $relatedItem);
            $this->processSubTotalFor($itemWithPromoCodes, $relatedItem);
        }
    }

    private function getTotal(): float
    {
        return round($this->total, 2);
    }

    private function getSubTotal(): float
    {
        return round($this->subTotal, 2);
    }

    private function createOrder()
    {
        $this->order = $this->ordersRepository->create([
            'reservation_id' => $this->validateCreateOrder->getReservationId(),
            'total' => $this->getTotal(),
            'sub_total' => $this->getSubTotal(),
        ]);
    }

    private function attachItemsAndDecreaseQuantities()
    {
        $toBeAttached = [];
        foreach ($this->validateCreateOrder->getRequestItems() as $itemRequest) {
            $relatedItem = $this->validateCreateOrder->getRelatedItems()->where('id', $itemRequest['id'])->first();
            $relatedItem->decrement('qty', $itemRequest['qty']);
            $relatedItem->save();
            $toBeAttached[] = [
                'order_id' => $this->order->id,
                'item_id' => $relatedItem->id,
                'price' => $relatedItem->price,
                'qty' => $itemRequest['qty'],
                'promo_codes' => array_key_exists('promo_codes', $itemRequest)
                    ? json_encode($itemRequest['promo_codes'])
                    : null
            ];
        }
        $this->itemsOrdersPromoCodesRepository->insert($toBeAttached);
    }

    private function reduceQuantities()
    {

    }

    public function validRequest(): bool
    {
        return $this->validateCreateOrder->validate();
    }

    public function getErrors(): array
    {
        return $this->validateCreateOrder->getErrors();
    }

    public function create(): bool
    {
        $this->calculate();
        DB::beginTransaction();
        try {
            $this->createOrder();
            $this->attachItemsAndDecreaseQuantities();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
