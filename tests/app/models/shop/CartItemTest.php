<?php

declare(strict_types=1);

namespace App\Models\Shop;

use CodeIgniter\Test\CIUnitTestCase;

class CartItemTest extends CIUnitTestCase
{
    public function test_インスタンス化できる(): void
    {
        $item = new CartItem(
            1,
            1,
            'CodeIgniter徹底入門',
            3800
        );

        $this->assertInstanceOf(CartItem::class, $item);
    }

    public function test_配列に変換できる(): void
    {
        $item = new CartItem(
            1,
            1,
            'CodeIgniter徹底入門',
            3800
        );

        $expected = [
            'id' => 1,
            'qty' => 1,
            'name' => 'CodeIgniter徹底入門',
            'price' => 3800,
            'amount' => 3800,
        ];
        $this->assertSame($expected, $item->asArray());
    }
}
