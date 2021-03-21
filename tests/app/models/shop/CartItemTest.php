<?php

declare(strict_types=1);

namespace App\Models\Shop;

use App\Exception\LogicException;
use CodeIgniter\Test\CIUnitTestCase;

class CartItemTest extends CIUnitTestCase
{
    /** @var CartItem */
    private $item;

    public function setUp(): void
    {
        parent::setUp();

        $this->item = new CartItem(
            1,
            1,
            'CodeIgniter徹底入門',
            3800
        );
    }

    public function test_インスタンス化できる(): void
    {
        $this->assertInstanceOf(CartItem::class, $this->item);
    }

    public function test_配列としてアクセスできる(): void
    {
        $expected = [
            'id' => 1,
            'qty' => 1,
            'name' => 'CodeIgniter徹底入門',
            'price' => 3800,
            'amount' => 3800,
        ];
        $this->assertSame($expected['id'], $this->item['id']);
        $this->assertSame($expected['name'], $this->item['name']);
    }

    public function test_配列に変換できる(): void
    {
        $expected = [
            'id' => 1,
            'qty' => 1,
            'name' => 'CodeIgniter徹底入門',
            'price' => 3800,
            'amount' => 3800,
        ];
        $this->assertSame($expected, $this->item->asArray());
    }

    public function test_存在しない配列キーにアクセスすると例外が返る(): void
    {
        $this->expectException(LogicException::class);

        $this->item['not_exists'];
    }

    public function test_存在しないプロパティにアクセスすると例外が返る(): void
    {
        $this->expectException(LogicException::class);

        $this->item->not_exists;
    }

    public function test_配列の値を変更しようとすると例外が返る(): void
    {
        $this->expectException(LogicException::class);

        $this->item['not_exists'] = 'new value';
    }

    public function test_配列の要素を削除しようとすると例外が返る(): void
    {
        $this->expectException(LogicException::class);

        unset($this->item['not_exists']);
    }
}
