<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;
use Kenjis\CI3Compatible\Test\Traits\SessionTest;
use Tests\Support\Libraries\Mock_Libraries_Email;

class Shop_model_test extends UnitTestCase
{
    use SessionTest;

    /** @var ShopModel */
    private $obj;

    public function setUp(): void
    {
        parent::setUp();

        $this->obj = $this->newModel(ShopModel::class);
        $this->CI->email = new Mock_Libraries_Email();
        $this->CI->admin = 'admin@example.jp';
    }

    public function test_order(): void
    {
        $this->CI->cartModel->add(1, 1);
        $this->CI->cartModel->add(2, 2);

        $actual = $this->obj->order('admin@example.jp');
        $this->assertTrue($actual);

        $mail = $this->CI->email->_get_data();
        $this->assertEquals($this->CI->admin, $mail['from']);
        $this->assertStringContainsString('注文合計： 11,400円', $mail['message']);
    }

    public function test_order_mail_fails(): void
    {
        $this->CI->email->return_send = false;

        $this->CI->cartModel->add(1, 1);
        $this->CI->cartModel->add(2, 2);

        $actual = $this->obj->order('admin@example.jp');
        $this->assertFalse($actual);
    }
}
