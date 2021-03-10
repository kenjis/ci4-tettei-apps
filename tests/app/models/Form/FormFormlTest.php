<?php

declare(strict_types=1);

namespace App\Models\Form;

use CodeIgniter\Test\CIUnitTestCase;

class FormFormlTest extends CIUnitTestCase
{
    /** @var array<string, string> */
    private $data;

    private function createForm(): FormForm
    {
        $this->data = [
            'name' => '名前です',
            'email' => 'メアドです',
            'comment' => 'コメントです',
        ];

        return new FormForm($this->data);
    }

    public function test_インスタンス化できる(): void
    {
        $form = $this->createForm();

        $this->assertInstanceOf(FormForm::class, $form);
    }

    public function test_配列に変換できる(): void
    {
        $form = $this->createForm();

        $this->assertSame($this->data, $form->asArray());
    }

    public function test_nameとemailがtrimされる(): void
    {
        $data = [
            'name' => '   名前です',
            'email' => ' メアドです ',
            'comment' => ' コメントです',
        ];
        $form = new FormForm($data);

        $expected = [
            'name' => '名前です',
            'email' => 'メアドです',
            'comment' => ' コメントです',
        ];
        $this->assertSame($expected, $form->asArray());
    }

    public function test_バリデーションルールを取得できる(): void
    {
        $form = $this->createForm();

        $this->assertIsArray($form->getValidationRules());
    }
}
