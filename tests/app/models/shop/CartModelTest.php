<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;
use Kenjis\CI3Compatible\Test\Traits\SessionTest;

use function get_instance;

class CartModelTest extends UnitTestCase
{
    use SessionTest;

    /** @var CartModel */
    private $cartModel;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $CI =& get_instance();
        $CI->load->library('seeder');
        $CI->seeder->call('ProductSeeder');
    }

    public function setUp(): void
    {
        parent::setUp();

        // CartModel が Session に依存しているためリセットする
        $_SESSION = [];

        $this->cartModel = new CartModel(new InventoryModel());
    }

    public function test_add(): void
    {
        $this->cartModel->add(1, 1);
        $this->cartModel->add(2, 2);

        $actual = $this->cartModel->count();

        $this->assertEquals(2, $actual);
    }

    public function test_delete(): void
    {
        $this->cartModel->add(2, 2);
        $this->cartModel->add(2, 0);

        $actual = $this->cartModel->count();

        $this->assertEquals(0, $actual);
    }

    public function test_get_all(): void
    {
        $this->cartModel->add(1, 1);
        $this->cartModel->add(1, 1);
        $this->cartModel->add(2, 2);

        $actual = $this->cartModel->getAll();

        $expected = [
            'items' => [
                1 => new CartItem(
                    1,
                    1,
                    'CodeIgniter徹底入門',
                    3800
                ),
                2 => new CartItem(
                    2,
                    2,
                    'CodeIgniter徹底入門 CD',
                    3800
                ),
            ],
            'line' => 2,
            'total' => 11400,
        ];
        $this->assertEquals($expected, $actual);
    }
}
