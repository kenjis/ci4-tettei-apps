<?php

declare(strict_types=1);

namespace App\Models\Shop;

use CodeIgniter\Test\CIUnitTestCase;

class CustomerInfoFormTest extends CIUnitTestCase
{
    /** @var array<string, string> */
    private $data;

    private function createForm(): CustomerInfoForm
    {
        $this->data = [
            'name'  => '名前',
            'zip'   => '111-1111',
            'addr'  => '東京都千代田区',
            'tel'   => '03-3333-3333',
            'email' => 'foo@example.jp',
        ];

        $form = new CustomerInfoForm();
        $form->getValidationRules();
        $form->setData($this->data);

        return $form;
    }

    public function test_配列としてアクセスできる(): void
    {
        $form = $this->createForm();

        $this->assertSame($this->data['name'], $form['name']);
    }

    public function test_イテレートできる(): void
    {
        $form = $this->createForm();

        $array = [];
        foreach ($form as $key => $val) {
            $array[$key] = $val;
        }

        $this->assertSame($this->data, $array);
    }
}
