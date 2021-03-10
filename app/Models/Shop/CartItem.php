<?php

declare(strict_types=1);

namespace App\Models\Shop;

/**
 * 買い物かごの中の商品
 */
class CartItem
{
    /** @var int 商品ID */
    private $id;

    /** @var int 数量 */
    private $qty;

    /** @var string 商品名 */
    private $name;

    /** @var int 単価 */
    private $price;

    /** @var int 金額 */
    private $amount;

    public function __construct(int $id, int $qty, string $name, int $price)
    {
        $this->id = $id;
        $this->qty = $qty;
        $this->name = $name;
        $this->price = $price;

        $this->amount = $qty * $price;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return array{id: int, qty: int, name: string, price: int, amount: int}
     */
    public function asArray(): array
    {
        return [
            'id' => $this->id,
            'qty' => $this->qty,
            'name' => $this->name,
            'price' => $this->price,
            'amount' => $this->amount,
        ];
    }
}
