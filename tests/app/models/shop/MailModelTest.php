<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Library\CI_Email;
use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;

class MailModelTest extends UnitTestCase
{
    /** @var MailModel */
    private $mailModel;

    /** @var CI_Email */
    private $ciEmail;

    public function setUp(): void
    {
        parent::setUp();

        $this->ciEmail = $this->getDouble(
            CI_Email::class,
            ['send' => true],
            true
        );
        $this->mailModel = new MailModel($this->ciEmail);
    }

    public function test_sendmail(): void
    {
        $mail['from_name'] = 'CIショップ';
        $mail['from']      = 'from@example.jp';
        $mail['to']        = 'to@example.org';
        $mail['bcc']       = 'admin@exaple.jp';
        $mail['subject']   = '【注文メール】CIショップ';
        $mail['body']      = 'CIショップにご注文いただきありがとうございます。';
        $actual = $this->mailModel->sendmail($mail);

        $this->assertTrue($actual);
    }

    public function test_sendmail_fail(): void
    {
        $this->ciEmail = $this->getDouble(
            CI_Email::class,
            ['send' => false],
            true
        );
        $this->mailModel = new MailModel($this->ciEmail);

        $mail['from_name'] = 'CIショップ';
        $mail['from']      = 'from@example.jp';
        $mail['to']        = 'to@example.org';
        $mail['bcc']       = 'admin@exaple.jp';
        $mail['subject']   = '【注文メール】CIショップ';
        $mail['body']      = 'CIショップにご注文いただきありがとうございます。';
        $actual = $this->mailModel->sendmail($mail);

        $this->assertFalse($actual);
    }
}
