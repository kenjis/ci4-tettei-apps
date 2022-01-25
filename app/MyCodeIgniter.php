<?php

namespace App;

use App\Module\AppModule;
use CodeIgniter\CodeIgniter;
use CodeIgniter\Controller;
use Config\Services;
use Ray\Di\Injector;

class MyCodeIgniter extends CodeIgniter
{
    protected function createController()
    {
        assert(is_string($this->controller), 'クロージャコントローラは使えません。');

        /**
         * @var class-string $controllerName
         */
        $controllerName = $this->controller;

        $injector = new Injector(new AppModule);

        $controller = $injector->getInstance($controllerName);
        assert($controller instanceof Controller);

        $controller->initController($this->request, $this->response, Services::logger());

        $this->benchmark->stop('controller_constructor');

        return $controller;
    }
}
