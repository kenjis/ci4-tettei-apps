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

use CodeIgniter\Router\RouteCollection;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Provides a base class with the trait for doing full HTTP testing
 * against your application.
 */
class CI4FeatureTestCase extends TestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    /**
     * If present, will override application
     * routes when using call().
     *
     * @var RouteCollection
     */
    protected $routes;

    /**
     * Values to be set in the SESSION global
     * before running the test.
     *
     * @var array
     */
    protected $session = [];

    /**
     * Enabled auto clean op buffer after request call
     *
     * @var bool
     */
    protected $clean = true;

    /**
     * Custom request's headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Allows for formatting the request body to what
     * the controller is going to expect
     *
     * @var string
     */
    protected $bodyFormat = '';

    /**
     * Allows for directly setting the body to what
     * it needs to be.
     *
     * @var mixed
     */
    protected $requestBody = '';
}
