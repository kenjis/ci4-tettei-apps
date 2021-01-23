<?php

declare(strict_types=1);

namespace App\Controllers;

use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Core\CI_Output;

/**
 * @property CI_Output $output
 */
class Home extends CI_Controller
{
    public function index(): void
    {
        $this->output->set_header('Content-Type: text/html; charset=UTF-8');
        $this->load->helper('url');
        $this->load->view('index');
    }
}
