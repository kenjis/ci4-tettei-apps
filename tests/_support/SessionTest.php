<?php

declare(strict_types=1);

namespace Tests\Support;

use CodeIgniter\Session\Handlers\ArrayHandler;
use CodeIgniter\Session\SessionInterface;
use CodeIgniter\Test\Mock\MockSession;
use Config\Services;

trait SessionTest
{
    /** @var SessionInterface */
    protected $session;

    /**
     * Pre-loads the mock session driver into $this->session.
     *
     * @before
     */
    protected function mockSession(): void
    {
        $config        = config('App');
        $this->session = new MockSession(new ArrayHandler($config, '0.0.0.0'), $config);
        Services::injectMock('session', $this->session);
    }
}
