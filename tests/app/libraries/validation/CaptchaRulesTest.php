<?php

declare(strict_types=1);

namespace App\Libraries\Validation;

use App\Controllers\Bbs;
use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;

class CaptchaRulesTest extends UnitTestCase
{
    public function test_captcha_check_failure(): void
    {
        $controller = $this->newController(Bbs::class);
        $controller->load->database();

        $validationRule = new CaptchaRules();

        $data = [
            'name' => '発火太郎',
            'email' => 'test@example.jp',
            'subject' => '新しい投稿',
            'body' => '新しい投稿です。',
            'password' => 'secret',
            'captcha' => 'bad_input',
            'key' => '100',
        ];
        $error = '';
        $actual = $validationRule->captcha_check(
            'bad_input',
            '100',
            $data,
            $error
        );

        $this->assertFalse($actual);

        $expected = '画像認証コードが一致しません。';
        $this->assertSame($expected, $error);
    }
}
