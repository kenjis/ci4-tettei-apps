<?php

declare(strict_types=1);

namespace App\Libraries\Validation;

use Kenjis\CI3Compatible\Test\TestCase\TestCase;

use function array_reduce;

class Shop_validation_customer_test extends TestCase
{
    /** @var shopValidationCustomer */
    private $obj;

    public function setUp(): void
    {
        parent::setUp();

        // Form_validaton::set_rules()が影響を受けるので、インスタンス生成前に
        // POSTメソッドにしておく必要がある
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->resetServices();
        $this->resetInstance();

        $this->CI->load->library('validation/shopValidationCustomer');
        $this->obj = $this->CI->shopValidationCustomer;
    }

    public function test_run_empty_data(): void
    {
        $_POST = [];
        $this->assertFalse($this->obj->run());
    }

    public function test_run_minimum_valid_data(): void
    {
        $_POST = [
            'name' => 'abc',
            'addr' => 'abc',
            'tel' => '03-3333-3333',
            'email' => 'foo@example.jp',
        ];

        $error_string = array_reduce(
            $this->obj->error_array(),
            static function ($carry, $item) {
                $carry .= $item . "\n";

                return $carry;
            },
            ''
        );
        $this->assertTrue($this->obj->run(), $error_string);
    }

    public function test_run_name_error(): void
    {
        $_POST = [
            'name' => '',
            'addr' => 'abc',
            'tel' => '03-3333-3333',
            'email' => 'xxx@example.jp',
        ];

        $this->obj->run();

        $test = $this->obj->error_array();
        $expected = ['name' => '名前 は必須項目です。'];
        $this->assertEquals($expected, $test);
    }
}
