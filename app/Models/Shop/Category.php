<?php

declare(strict_types=1);

namespace App\Models\Shop;

use App\Libraries\Traits\ArrayReadable;
use ArrayAccess;

/**
 * カテゴリ
 *
 * @implements ArrayAccess<string, int|string>
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class Category implements ArrayAccess
{
    use ArrayReadable;

    /** @var string[] */
    private $arrayReadProperties = [ // phpcs:ignore
        'id',
        'name',
    ];

    /** @var int カテゴリID */
    private $id; // phpcs:ignore

    /** @var string カテゴリ名 */
    private $name; // phpcs:ignore

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
