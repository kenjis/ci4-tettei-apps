<?php

declare(strict_types=1);

namespace App\Models\Bbs;

use CodeIgniter\Test\CIUnitTestCase;

class PostFormTest extends CIUnitTestCase
{
    /** @var array<string, string> */
    private $data;

    private function createForm(): PostForm
    {
        $this->data = [
            'name' => '削除太郎',
            'email' => 'test@example.jp',
            'subject' => '削除する投稿',
            'body' => 'この投稿を削除します。',
            'password' => 'delete',
            'captcha' => '8888',
            'key' => '100',
        ];

        $form = new PostForm();
        $form->getValidationRules('confirm');
        $form->setData($this->data);

        return $form;
    }

    public function test_配列としてアクセスできる(): void
    {
        $form = $this->createForm();

        $this->assertSame($this->data['name'], $form['name']);
    }

    public function test_keyがintに変換される(): void
    {
        $form = $this->createForm();

        $this->assertSame(100, $form['key']);
    }

    public function test_イテレートできる(): void
    {
        $form = $this->createForm();

        $array = [];
        foreach ($form as $key => $val) {
            $array[$key] = $val;
        }

        $this->assertCount(7, $array);
    }

    public function test_バリデーションルールを取得できる_グループ指定なし(): void
    {
        $form = $this->createForm();
        $rules = $form->getValidationRules();

        $expected = [
            'name' => [
                'label' => '名前',
                'rules' => 'trim|required|max_length[16]',
            ],
            'email' => [
                'label' => 'メールアドレス',
                'rules' => 'trim|permit_empty|valid_email|max_length[64]',
            ],
            'subject' => [
                'label' => '件名',
                'rules' => 'trim|required|max_length[32]',
            ],
            'body' => [
                'label' => '内容',
                'rules' => 'trim|required|max_length[200]',
            ],
            'password' => [
                'label' => '削除パスワード',
                'rules' => 'max_length[32]',
            ],
        ];
        $this->assertSame($expected, $rules);
    }

    public function test_バリデーションルールを取得できる_グループ指定confirm(): void
    {
        $form = $this->createForm();
        $rules = $form->getValidationRules('confirm');

        $expected = [
            'name' => [
                'label' => '名前',
                'rules' => 'trim|required|max_length[16]',
            ],
            'email' => [
                'label' => 'メールアドレス',
                'rules' => 'trim|permit_empty|valid_email|max_length[64]',
            ],
            'subject' => [
                'label' => '件名',
                'rules' => 'trim|required|max_length[32]',
            ],
            'body' => [
                'label' => '内容',
                'rules' => 'trim|required|max_length[200]',
            ],
            'password' => [
                'label' => '削除パスワード',
                'rules' => 'max_length[32]',
            ],
            'captcha' => [
                'label' => '画像認証コード',
                'rules' => 'trim|required|alpha_numeric|captcha_check',
            ],
            'key' => [
                'label' => 'key',
                'rules' => 'numeric',
            ],
        ];
        $this->assertSame($expected, $rules);
    }

    public function test_グループ指定なしの場合はcommonに含まれるキーのみになる(): void
    {
        $this->data = [
            'name' => '削除太郎',
            'email' => 'test@example.jp',
            'subject' => '削除する投稿',
            'body' => 'この投稿を削除します。',
            'password' => 'delete',
        ];

        $form = new PostForm();
        $form->getValidationRules();
        $form->setData($this->data);

        $this->assertSame($this->data, $form->asArray());
    }
}
