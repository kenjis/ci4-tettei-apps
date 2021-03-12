<?php

declare(strict_types=1);

namespace App\Models\Shop;

use App\Libraries\Traits\ArrayReadable;
use ArrayAccess;

/**
 * 商品
 *
 * @property-read int $id
 * @property-read int $categoryId
 * @property-read string $name
 * @property-read string $detail
 * @property-read int $price
 * @property-read string $img
 * @implements ArrayAccess<string, int|string>
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class Product implements ArrayAccess
{
    use ArrayReadable;

    /** @var string[] */
    private $arrayReadProperties = [ // phpcs:ignore
        'id',
        'categoryId',
        'name',
        'detail',
        'price',
        'img',
    ];

    /** @var int 商品ID */
    private $id; // phpcs:ignore

    /** @var int カテゴリID */
    private $categoryId; // phpcs:ignore

    /** @var string 商品名 */
    private $name; // phpcs:ignore

    /** @var string 詳細 */
    private $detail; // phpcs:ignore

    /** @var int 単価 */
    private $price; // phpcs:ignore

    /** @var string 商品画像ファイル名 */
    private $img; // phpcs:ignore

    public function __construct(
        int $id,
        int $categoryId,
        string $name,
        string $detail,
        int $price,
        string $img
    ) {
        $this->id = $id;
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->detail = $detail;
        $this->price = $price;
        $this->img = $img;
    }
}
