<?php

declare(strict_types=1);

namespace App\Controllers;

use Kenjis\CI3Compatible\Library\CI_Email;
use Kenjis\CI3Compatible\Test\TestCase\FeatureTestCase;

class FormTest extends FeatureTestCase
{
    public function test_index(): void
    {
        $output = $this->request('GET', 'form');
        $this->assertStringContainsString('<title>コンタクトフォーム</title>', $output);
    }

    public function test_confirm_error(): void
    {
        $output = $this->request('POST', 'form/confirm', ['name' => '']);
        $this->assertStringContainsString('名前 は必須項目です', $output);
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
        $this->assertStringContainsString('お問い合わせ内容の確認', $output);
        $this->assertStringContainsString('&lt;s&gt;abc&lt;/s&gt;', $output);
        $this->assertStringNotContainsString('<s>abc</s>', $output);
    }

    public function test_send(): void
    {
        $this->request->setCallable(
            function ($CI): void {
                $email = $this->getDouble(
                    CI_Email::class,
                    ['send' => true],
                    true
                );
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
        $this->assertStringContainsString('送信しました', $output);
    }
}
