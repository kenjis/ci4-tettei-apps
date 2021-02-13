<?php

declare(strict_types=1);

class Form_test extends TestCase
{
    public function test_index(): void
    {
        $output = $this->request('GET', 'form');
        $this->assertContains('<title>コンタクトフォーム</title>', $output);
    }

    public function test_confirm_error(): void
    {
        $output = $this->request('POST', 'form/confirm', ['name' => '']);
        $this->assertContains('名前欄は必須フィールドです', $output);
    }

    public function test_confirm_ok(): void
    {
        $output = $this->request(
            'POST',
            'form/confirm',
            [
                'name' => '<s>abc</s>',
                'email' => 'test@example.jp',
                'comment' => '<s>abc</s>',
            ]
        );
        $this->assertContains('お問い合わせ内容の確認', $output);
        $this->assertContains('&lt;s&gt;abc&lt;/s&gt;', $output);
        $this->assertNotContains('<s>abc</s>', $output);
    }

    public function test_send(): void
    {
        $this->request->setCallable(
            function ($CI): void {
                $email = $this->getDouble('CI_Email', ['send' => true]);
                $CI->email = $email;
            }
        );
        $output = $this->request(
            'POST',
            'form/send',
            [
                'name' => '<s>abc</s>',
                'email' => 'test@example.jp',
                'comment' => '<s>abc</s>',
            ]
        );
        $this->assertContains('送信しました', $output);
    }
}
