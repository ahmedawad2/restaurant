<?php

namespace App\Infra\Classes\BusinessLogic\Orders;

use App\Infra\Interfaces\Repositories\ItemsOrdersPromoCodesRepositoryInterface;
use App\Infra\Interfaces\Repositories\ItemsPromoCodesRepositoryInterface;
use App\Infra\Interfaces\Repositories\ItemsRepositoryInterface;
use App\Infra\Interfaces\Repositories\OrdersRepositoryInterface;
use App\Infra\Traits\TriggerErrorsTrait;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateOrder
{
    use TriggerErrorsTrait;

    private OrdersRepositoryInterface $ordersRepository;
    private ItemsRepositoryInterface $itemsRepository;
    private ItemsOrdersPromoCodesRepositoryInterface $itemsOrdersPromoCodesRepository;
    private ItemsPromoCodesRepositoryInterface $itemsPromoCodesRepository;
    private ValidateCreateOrder $validateCreateOrder;
    private Order $order;
    private float $total = 0;
    private float $subTotal = 0;

    public function __construct(OrdersRepositoryInterface $ordersRepository, ItemsOrdersPromoCodesRepositoryInterface $itemsOrdersPromoCodesRepository,
                                ValidateCreateOrder       $validateCreateOrder, ItemsRepositoryInterface $itemsRepository, ItemsPromoCodesRepositoryInterface $itemsPromoCodesRepository)
    {
        $this->validateCreateOrder = $validateCreateOrder;
        $this->ordersRepository = $ordersRepository;
        $this->itemsOrdersPromoCodesRepository = $itemsOrdersPromoCodesRepository;
        $this->itemsPromoCodesRepository = $itemsPromoCodesRepository;
        $this->itemsRepository = $itemsRepository;
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

    private function attachItemsAndPromoCodes()
    {
        $itemsToBeAttached = [];
        $promoCodesToMarkAsConsumed = [];
        foreach ($this->validateCreateOrder->getRequestItems() as $itemRequest) {
            $relatedItem = $this->validateCreateOrder->getRelatedItems()->where('id', $itemRequest['id'])->first();
            if (array_key_exists('promo_codes', $itemRequest)) {
                $promo = $relatedItem->validPromoCodes->whereIn('id', $itemRequest['promo_codes']);
                $promoCodesToMarkAsConsumed = array_merge($promo->pluck('pivot.id')->toArray(), $promoCodesToMarkAsConsumed);
            }
            $itemsToBeAttached[] = [
                'order_id' => $this->order->id,
                'item_id' => $relatedItem->id,
                'price' => $relatedItem->price,
                'qty' => $itemRequest['qty'],
                'promo_codes' => array_key_exists('promo_codes', $itemRequest)
                    ? json_encode($itemRequest['promo_codes'])
                    : null
            ];
        }
        $this->itemsPromoCodesRepository
            ->whereIn('id', array_unique($promoCodesToMarkAsConsumed))
            ->update([
                'consumed_by' => Auth::guard('customers')->id()
            ]);
        $this->itemsOrdersPromoCodesRepository->insert($itemsToBeAttached);
    }

    private function decreaseQTYs()
    {
        $idsWithQty = [];
        foreach ($this->validateCreateOrder->getRequestItems() as $itemRequest) {
            if (!in_array($itemRequest['id'], array_keys($idsWithQty))) {
                $idsWithQty[$itemRequest['id']] = $itemRequest['qty'];
            } else {
                $idsWithQty[$itemRequest['id']] += $itemRequest['qty'];
            }
        }
        $this->itemsRepository = $this->itemsRepository->decreaseQty($idsWithQty);
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
            $this->attachItemsAndPromoCodes();
            $this->decreaseQTYs();
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
