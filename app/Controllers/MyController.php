<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exception\RuntimeException;
use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Core\CI_Output;

/**
 * CodeIgniter3 MY_Controller
 *
 * @property CI_Output $output
 */
class MyController extends CI_Controller
{
    protected function postOnly(): void
    {
        if ($this->request->getMethod() !== 'post') {
            throw new RuntimeException('不正な入力です。', 400);
        }
    }
}
