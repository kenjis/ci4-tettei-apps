<?php

declare(strict_types=1);

class Shop_model_test extends UnitTestCase
{
    public function setUp(): void
    {
        $this->obj = $this->newModel(Shop_model::class);
        $this->CI->email = new Mock_Libraries_Email();
        $this->CI->admin = 'admin@example.jp';
    }

    public function test_order(): void
    {
        $this->CI->cart_model->add(1, 1);
        $this->CI->cart_model->add(2, 2);

        $actual = $this->obj->order();
        $this->assertTrue($actual);

        $mail = $this->CI->email->_get_data();
        $this->assertEquals($this->CI->admin, $mail['from']);
        $this->assertStringContainsString('注文合計： 11,400円', $mail['message']);
    }

    public function test_order_mail_fails(): void
    {
        $this->CI->email->return_send = false;

        $this->CI->cart_model->add(1, 1);
        $this->CI->cart_model->add(2, 2);

        $actual = $this->obj->order();
        $this->assertFalse($actual);
    }
}
