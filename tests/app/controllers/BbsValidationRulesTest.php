<?php

declare(strict_types=1);

namespace App\Controllers;

use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;

class BbsValidationRulesTest extends UnitTestCase
{
    public function test_captcha_check_failure(): void
    {
        $controller = $this->newController(Bbs::class);
        $controller->load->database();

        $validationRule = new BbsValidationRules();

        $error = '';
        $actual = $validationRule->captcha_check('bad_input', $error);

        $this->assertFalse($actual);

        $expected = '画像認証コードが一致しません。';
        $this->assertSame($expected, $error);
    }
}
