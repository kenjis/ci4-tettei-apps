<?php

declare(strict_types=1);

namespace App\Module;

use App\Libraries\GeneratePagination;
use App\Libraries\Validation\FieldValidation;
use App\Libraries\Validation\FormValidation;
use App\Models\Shop\MailService;
use CodeIgniter\Validation\ValidationInterface;
use Config\Services;
use Kenjis\CI4Twig\Twig;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

class LibraryModule extends AbstractModule
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->bind(ValidationInterface::class)->toInstance(Services::validation());

        $this->bind(Twig::class)->in(Scope::SINGLETON);
        $this->bind(GeneratePagination::class)->in(Scope::SINGLETON);
        $this->bind(FormValidation::class)->in(Scope::SINGLETON);
        $this->bind(FieldValidation::class)->in(Scope::SINGLETON);
        $this->bind(MailService::class)->in(Scope::SINGLETON);
    }
}
