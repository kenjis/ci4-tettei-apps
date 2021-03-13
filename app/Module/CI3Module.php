<?php

declare(strict_types=1);

namespace App\Module;

use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Database\CI_DB;
use Kenjis\CI3Compatible\Library\CI_Email;
use Kenjis\CI3Compatible\Library\CI_Parser;
use Kenjis\CI3Compatible\Library\CI_Session;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

class CI3Module extends AbstractModule
{
    /** @var CI_Controller */
    private $CI;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->bind_CI_DB();
        $this->bind(CI_Session::class)->in(Scope::SINGLETON);
        $this->bind(CI_Email::class)->in(Scope::SINGLETON);
        $this->bind(CI_Parser::class)->in(Scope::SINGLETON);
    }

    private function bind_CI_DB(): void // phpcs:ignore
    {
        $this->CI = get_instance();

        $this->CI->load->database();
        $db = $this->CI->db; // @phpstan-ignore-line

        $this->bind(CI_DB::class)->toInstance($db);
    }
}
