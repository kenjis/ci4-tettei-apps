<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Library\CI_Email;
use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;

class MailServiceTest extends UnitTestCase
{
    /** @var MailService */
    private $mailService;

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
        $this->mailService = new MailService($this->ciEmail);
    }

    public function test_sendmail(): void
    {
        $mail['from_name'] = 'CIショップ';
        $mail['from']      = 'from@example.jp';
        $mail['to']        = 'to@example.org';
        $mail['bcc']       = 'admin@exaple.jp';
        $mail['subject']   = '【注文メール】CIショップ';
        $mail['body']      = 'CIショップにご注文いただきありがとうございます。';
        $actual = $this->mailService->sendmail($mail);

        $this->assertTrue($actual);
    }

    public function test_sendmail_fail(): void
    {
        $this->ciEmail = $this->getDouble(
            CI_Email::class,
            ['send' => false],
            true
        );
        $this->mailService = new MailService($this->ciEmail);

        $mail['from_name'] = 'CIショップ';
        $mail['from']      = 'from@example.jp';
        $mail['to']        = 'to@example.org';
        $mail['bcc']       = 'admin@exaple.jp';
        $mail['subject']   = '【注文メール】CIショップ';
        $mail['body']      = 'CIショップにご注文いただきありがとうございます。';
        $actual = $this->mailService->sendmail($mail);

        $this->assertFalse($actual);
    }
}
