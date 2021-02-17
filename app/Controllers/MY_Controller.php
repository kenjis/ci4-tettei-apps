<?php

declare(strict_types=1);

namespace App\Controllers;

use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Core\CI_Output;

/**
 * @property CI_Output $output
 */
class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (ENVIRONMENT !== 'development') {
            return;
        }
    }
}
