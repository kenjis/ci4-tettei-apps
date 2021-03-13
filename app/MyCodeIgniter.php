<?php

namespace App;

use App\Module\AppModule;
use CodeIgniter\CodeIgniter;
use Config\Services;
use Ray\Di\Injector;

class MyCodeIgniter extends CodeIgniter
{
    protected function createController()
    {
        assert(is_string($this->controller), 'クロージャコントローラは使えません。');

        $injector = new Injector(new AppModule);

        $controller = $injector->getInstance($this->controller);
        $controller->initController($this->request, $this->response, Services::logger());

        $this->benchmark->stop('controller_constructor');

        return $controller;
    }
}
