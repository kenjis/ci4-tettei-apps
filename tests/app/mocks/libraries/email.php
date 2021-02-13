<?php

declare(strict_types=1);

/**
 * Part of CI PHPUnit Test
 *
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Mock_Libraries_Email
{
    private $data = [];

    /** @var bool return value of send() */
    public $return_send = true;

    public function initialize(): void
    {
    }

    public function from($from): void
    {
        $this->data['from'] = $from;
    }

    public function to($to): void
    {
        $this->data['to'] = $to;
    }

    public function bcc($bcc): void
    {
        $this->data['bcc'] = $bcc;
    }

    public function subject($subject): void
    {
        $this->data['subject'] = $subject;
    }

    public function message($message): void
    {
        $this->data['message'] = $message;
    }

    public function send()
    {
        return $this->return_send;
    }

    public function _get_data()
    {
        return $this->data;
    }
}
