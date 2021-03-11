<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;
use Kenjis\CI3Compatible\Test\Traits\SessionTest;

class CustomerModelTest extends UnitTestCase
{
    use SessionTest;

    /** @var CustomerModel */
    private $obj;

    public function setUp(): void
    {
        parent::setUp();

        $this->obj = $this->newModel(CustomerModel::class);
    }

    public function test_set_and_get(): void
    {
        $expected = [
            'name'  => '名前',
            'zip'   => '111-1111',
            'addr'  => '東京都千代田区',
            'tel'   => '03-3333-3333',
            'email' => 'foo@example.jp',
        ];
        $form = new CustomerInfoForm();
        $form->getValidationRules();
        $form->setData($expected);

        $this->obj->set($form);

        $actual = $this->obj->get();

        $this->assertEquals($expected, $actual);
    }
}
