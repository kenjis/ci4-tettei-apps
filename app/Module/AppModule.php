<?php

declare(strict_types=1);

namespace App\Module;

use App\Libraries\Validation\FieldValidation;
use App\Models\Shop\CartRepository;
use App\Models\Shop\ProductRepository;
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
        $this->bindFieldValidation();
        $this->bind(CartRepository::class)->in(Scope::SINGLETON);
        $this->bind(ProductRepository::class)->in(Scope::SINGLETON);
    }

    private function bindFieldValidation(): void
    {
        $fieldValidation = new FieldValidation(Services::validation());
        $this->bind(FieldValidation::class)->toInstance($fieldValidation);
    }
}
