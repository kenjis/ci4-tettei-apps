<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;
use Tests\Support\Libraries\Mock_Libraries_Email;

class MailModelTest extends UnitTestCase
{
    /** @var MailModel */
    private $obj;

    public function setUp(): void
    {
        parent::setUp();

        $this->obj = $this->newModel(MailModel::class);
        $this->CI->email = new Mock_Libraries_Email();
    }

    public function test_sendmail(): void
    {
        $mail['from_name'] = 'CIショップ';
        $mail['from']      = 'from@example.jp';
        $mail['to']        = 'to@example.org';
        $mail['bcc']       = 'admin@exaple.jp';
        $mail['subject']   = '【注文メール】CIショップ';
        $mail['body']      = 'CIショップにご注文いただきありがとうございます。';
        $actual = $this->obj->sendmail($mail);
        $this->assertTrue($actual);
    }

    public function test_sendmail_fail(): void
    {
        $mail['from_name'] = 'CIショップ';
        $mail['from']      = 'from@example.jp';
        $mail['to']        = 'to@example.org';
        $mail['bcc']       = 'admin@exaple.jp';
        $mail['subject']   = '【注文メール】CIショップ';
        $mail['body']      = 'CIショップにご注文いただきありがとうございます。';

        $this->CI->email->return_send = false;

        $actual = $this->obj->sendmail($mail);
        $this->assertFalse($actual);
    }
}
