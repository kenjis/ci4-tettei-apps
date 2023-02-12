<?php

declare(strict_types=1);

namespace App\Module;

use App\Libraries\GeneratePagination;
use App\Libraries\Validation\FieldValidation;
use App\Libraries\Validation\FormValidation;
use App\Models\Shop\AddToCartUseCase;
use App\Models\Shop\CartRepository;
use App\Models\Shop\CategoryRepository;
use App\Models\Shop\CustomerInfoRepository;
use App\Models\Shop\MailService;
use App\Models\Shop\OrderUseCase;
use App\Models\Shop\ProductRepository;
use CodeIgniter\Validation\Validation;
use Config\Services;
use Kenjis\CI4Twig\Twig;
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

        $this->bind(Twig::class)->in(Scope::SINGLETON);
        $this->bind(GeneratePagination::class)->in(Scope::SINGLETON);
        $this->bind(FormValidation::class)->in(Scope::SINGLETON);
        $this->bind(FieldValidation::class)->in(Scope::SINGLETON);
        $this->bind(MailService::class)->in(Scope::SINGLETON);

        $this->bind(CartRepository::class)->in(Scope::SINGLETON);
        $this->bind(ProductRepository::class)->in(Scope::SINGLETON);
        $this->bind(CategoryRepository::class)->in(Scope::SINGLETON);
        $this->bind(CustomerInfoRepository::class)->in(Scope::SINGLETON);

        $this->bind(AddToCartUseCase::class);
        $this->bind(OrderUseCase::class);
    }
}
