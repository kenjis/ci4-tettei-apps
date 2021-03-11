<?php

declare(strict_types=1);

namespace App\Models\Shop;

use App\Libraries\Traits\ArrayReadable;
use ArrayAccess;

/**
 * 買い物かごの中の商品
 *
 * @implements ArrayAccess<string, int|string>
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class CartItem implements ArrayAccess
{
    use ArrayReadable;

    /** @var string[] */
    private $arrayReadProperties = [ // phpcs:ignore
        'id',
        'qty',
        'name',
        'price',
        'amount',
    ];

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
}
