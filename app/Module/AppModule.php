<?php

declare(strict_types=1);

namespace App\Module;

use App\Models\Shop\AddToCartUseCase;
use App\Models\Shop\CartRepository;
use App\Models\Shop\CategoryRepository;
use App\Models\Shop\CustomerInfoRepository;
use App\Models\Shop\OrderUseCase;
use App\Models\Shop\ProductRepository;
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
        $this->install(new LibraryModule());

        $this->bind(CartRepository::class)->in(Scope::SINGLETON);
        $this->bind(ProductRepository::class)->in(Scope::SINGLETON);
        $this->bind(CategoryRepository::class)->in(Scope::SINGLETON);
        $this->bind(CustomerInfoRepository::class)->in(Scope::SINGLETON);

        $this->bind(AddToCartUseCase::class);
        $this->bind(OrderUseCase::class);
    }
}
