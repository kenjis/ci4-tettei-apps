<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Tests\Support\SessionTest;
use Tests\Support\UnitTestCase;

use function get_instance;

class Cart_model_test extends UnitTestCase
{
    use SessionTest;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $CI =& get_instance();
        $CI->load->library('seeder');
        $CI->seeder->call('ProductSeeder');
    }

    public function setUp(): void
    {
        // Cart_model が Session に依存しているためリセットする
        $_SESSION = [];

        $this->obj = $this->newModel(Cart_model::class);
    }

    public function test_add(): void
    {
        $this->obj->add(1, 1);
        $this->obj->add(2, 2);
        $actual = $this->obj->count();
        $this->assertEquals(2, $actual);
    }

    public function test_delete(): void
    {
        $this->obj->add(2, 2);
        $this->obj->add(2, 0);
        $actual = $this->obj->count();
        $this->assertEquals(0, $actual);
    }

    public function test_get_all(): void
    {
        $this->obj->add(1, 1);
        $this->obj->add(1, 1);
        $this->obj->add(2, 2);

        $actual = $this->obj->get_all();
        $expected = [
            'items' => [
                1 => [
                    'id' => 1,
                    'qty' => 1,
                    'name' => 'CodeIgniter徹底入門',
                    'price' => '3800',
                    'amount' => 3800,
                ],
                2 => [
                    'id' => 2,
                    'qty' => 2,
                    'name' => 'CodeIgniter徹底入門 CD',
                    'price' => '3800',
                    'amount' => 7600,
                ],
            ],
            'line' => 2,
            'total' => 11400,
        ];
        $this->assertEquals($expected, $actual);
    }
}
