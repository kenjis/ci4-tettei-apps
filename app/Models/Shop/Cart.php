<?php

declare(strict_types=1);

namespace App\Models\Shop;

use App\Libraries\SystemClock;

use function count;
use function number_format;

/**
 * 買い物かご
 */
class Cart
{
    /** @var array<int, CartItem> 商品 */
    private $items = [];

    /** @var int 合計 */
    private $total = 0;

    /** @var SystemClock */
    private $clock;

    public function __construct(?SystemClock $clock = null)
    {
        if ($clock === null) {
            $this->clock = new SystemClock();

            return;
        }

        $this->clock = $clock;
    }

    /**
     * 商品アイテム数を返す
     */
    public function getLineCount(): int
    {
        return count($this->items);
    }

    public function add(CartItem $item): void
    {
        $this->items[$item->getId()] = $item;
    }

    public function getTotal(): int
    {
        $this->total = 0;

        foreach ($this->items as $item) {
            $this->total += $item->getAmount();
        }

        return $this->total;
    }

    public function remove(int $productId): void
    {
        unset($this->items[$productId]);
    }

    /**
     * @return array<int, CartItem>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * 注文確認データを取得する
     *
     * @return array{date: string, items: array<int, array<string, mixed>>, line: int, total: string}
     */
    public function getOrderConfirmationData(): array
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            $itemArray = $item->asArray();

            $itemArray['price']  = number_format((float) $item->getPrice());
            $itemArray['amount'] = number_format((float) $item->getAmount());

            $items[] = $itemArray;
        }

        return [
            'date'  => $this->clock->now()->format('Y/m/d H:i:s'),
            'items' => $items,
            'line'  => $this->getLineCount(),
            'total' => number_format($this->getTotal()),
        ];
    }
}
