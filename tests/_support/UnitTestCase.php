<?php

declare(strict_types=1);

namespace Tests\Support;

use Kenjis\CI3Compatible\Core\CI_Model;

use function strrpos;
use function substr;

class UnitTestCase extends TestCase
{
    /**
     * Create a model instance
     */
    public function newModel(string $classname): CI_Model
    {
        $this->resetInstance();
        $this->CI->load->model($classname);

        // Is the model in a sub-folder?
        if (($last_slash = strrpos($classname, '/')) !== false) {
            $classname = substr($classname, ++$last_slash);
        }

        return $this->CI->$classname;
    }
}
