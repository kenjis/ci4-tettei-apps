<?php

declare(strict_types=1);

/**
 * This file is part of the CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Support;

use CodeIgniter\CodeIgniter;
use CodeIgniter\Test\CIUnitTestCase;
use Config\App;
use Config\Autoload;
use Config\Modules;
use Config\Services;
use Tests\Support\Mock\MockCodeIgniter;

/**
 * Framework test case for PHPUnit.
 */
abstract class TestCase extends CIUnitTestCase
{
    /**
     * Loads up an instance of CodeIgniter
     * and gets the environment setup.
     */
    protected function createApplication(): CodeIgniter
    {
        // Initialize the autoloader.
        Services::autoloader()->initialize(new Autoload(), new Modules());

        $app = new MockCodeIgniter(new App());
        $app->initialize();

        return $app;
    }
}
