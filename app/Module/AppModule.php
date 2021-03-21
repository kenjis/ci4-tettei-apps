<?php

declare(strict_types=1);

namespace App\Module;

use App\Models\Shop\CartRepository;
use App\Models\Shop\ProductRepository;
use CodeIgniter\Validation\Validation;
use Config\Services;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

class AppModule extends AbstractModule
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->install(new CI3Module());
        $this->bind(Validation::class)->toInstance(Services::validation());
        $this->bind(CartRepository::class)->in(Scope::SINGLETON);
        $this->bind(ProductRepository::class)->in(Scope::SINGLETON);
    }
}
