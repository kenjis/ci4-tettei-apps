<?php

declare(strict_types=1);

namespace App\Models\Shop;

use function count;

/**
 * 買い物かご
 */
class Cart
{
    /** @var CartItem[] 商品 */
    private $items = [];

    /** @var int 合計 */
    private $total = 0;

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
}
