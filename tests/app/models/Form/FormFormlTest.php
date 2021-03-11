<?php

declare(strict_types=1);

namespace App\Models\Form;

use CodeIgniter\Test\CIUnitTestCase;
use Kenjis\CI3Compatible\Exception\LogicException;

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

        $form = new FormForm();
        $form->setData($this->data);

        return $form;
    }

    public function test_インスタンス化できる(): void
    {
        $form = $this->createForm();

        $this->assertInstanceOf(FormForm::class, $form);
    }

    public function test_配列としてアクセスできる(): void
    {
        $form = $this->createForm();

        $this->assertSame($this->data['name'], $form['name']);
    }

    public function test_存在しないキーにアクセスすると例外が返る(): void
    {
        $form = $this->createForm();

        $this->expectException(LogicException::class);

        $form['not_exists'];
    }

    public function test_nameとemailがtrimされる(): void
    {
        $data = [
            'name' => '   名前です',
            'email' => ' メアドです ',
            'comment' => ' コメントです',
        ];
        $form = new FormForm();
        $form->setData($data);

        $expected = [
            'name' => '名前です',
            'email' => 'メアドです',
            'comment' => ' コメントです',
        ];
        $this->assertSame($expected['name'], $form['name']);
        $this->assertSame($expected['email'], $form['email']);
        $this->assertSame($expected['comment'], $form['comment']);
    }

    public function test_バリデーションルールを取得できる(): void
    {
        $form = $this->createForm();

        $this->assertIsArray($form->getValidationRules());
    }

    public function test_データをセットせずに取得すると例外が返る(): void
    {
        $this->expectException(LogicException::class);

        $form = new FormForm();

        $this->assertIsArray($form['name']);
    }
}
