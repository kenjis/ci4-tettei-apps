<?php

declare(strict_types=1);

namespace App\Models\Shop;

use App\Libraries\SystemClock;
use CodeIgniter\Test\CIUnitTestCase;

class CartTest extends CIUnitTestCase
{
    /** @var CartItem */
    private $item1;

    /** @var CartItem */
    private $item2;

    public function setUp(): void
    {
        parent::setUp();

        $this->item1 = new CartItem(
            1,
            1,
            'CodeIgniter徹底入門',
            3800
        );

        $this->item2 = new CartItem(
            2,
            1,
            'CodeIgniter徹底入門 CD',
            3800
        );
    }

    public function test_インスタンス化できる(): void
    {
        $cart = new Cart();

        $this->assertInstanceOf(Cart::class, $cart);
    }

    public function test_商品アイテム数を取得できる(): void
    {
        $cart = new Cart();

        $this->assertSame(0, $cart->getLineCount());
    }

    public function test_商品を追加できる(): void
    {
        $cart = new Cart();

        $cart->add($this->item1);
        $this->assertSame(1, $cart->getLineCount());

        $cart->add($this->item2);
        $this->assertSame(2, $cart->getLineCount());
    }

    public function test_合計金額を取得できる(): void
    {
        $cart = new Cart();

        $cart->add($this->item1);
        $this->assertSame(3800, $cart->getTotal());

        $cart->add($this->item2);
        $this->assertSame(7600, $cart->getTotal());
    }

    public function test_商品を削除できる(): void
    {
        $cart = new Cart();
        $cart->add($this->item1);
        $cart->add($this->item2);

        $cart->remove(1);

        $this->assertSame(1, $cart->getLineCount());
    }

    public function test_商品アイテムを取得できる(): void
    {
        $cart = new Cart();
        $cart->add($this->item1);
        $cart->add($this->item2);

        $expected = [
            1 => $this->item1,
            2 => $this->item2,
        ];
        $this->assertSame($expected, $cart->getItems());
    }

    public function test_注文確認データを取得できる(): void
    {
        $clock = new SystemClock();
        $clock->freeze('2021-03-10 12:31:45');

        $cart = new Cart($clock);
        $cart->add($this->item1);
        $cart->add($this->item2);

        $expected = [
            'date'  => '2021/03/10 12:31:45',
            'items' => [
                [
                    'id' => 1,
                    'qty' => 1,
                    'name' => 'CodeIgniter徹底入門',
                    'price' => '3,800',
                    'amount' => '3,800',
                ],
                [
                    'id' => 2,
                    'qty' => 1,
                    'name' => 'CodeIgniter徹底入門 CD',
                    'price' => '3,800',
                    'amount' => '3,800',
                ],
            ],
            'line'  => 2,
            'total' => '7,600',
        ];
        $this->assertSame($expected, $cart->getOrderConfirmationData());
    }
}
