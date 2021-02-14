<?php

declare(strict_types=1);

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;
use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Core\CoreLoader;

use function get_instance;

class TestCase extends CIUnitTestCase
{
    /** @var string */
    private $controller_class;

    /**
     * Reset CodeIgniter instance and assign new CodeIgniter instance as $this->CI
     */
    public function resetInstance(
        bool $use_my_controller = false,
        bool $reset_services = false
    ): void {
        if ($reset_services) {
            Services::reset(true);
        }

        new CoreLoader();

        $this->createCodeIgniterInstance($use_my_controller);
        $this->CI =& get_instance();
    }

    public function createCodeIgniterInstance(bool $use_my_controller = false): void
    {
        if ($use_my_controller) {
            new $this->controller_class();
        } else {
            new CI_Controller();
        }
    }
}
