<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Naming\Rector\Variable\UnderscoreToCamelCaseLocalVariableNameRector;
use Rector\Naming\Rector\Variable\UnderscoreToCamelCaseVariableNameRector;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();

    // paths to refactor; solid alternative to CLI arguments
    $parameters->set(Option::PATHS, [__DIR__ . '/app', __DIR__ . '/tests']);

    // is there a file you need to skip?
    $parameters->set(Option::SKIP, [
        __DIR__ . '/app/Config',
        __DIR__ . '/app/Database/Migrations',
        __DIR__ . '/app/Views',
        __DIR__ . '/tests',
    ]);

    // Rector relies on autoload setup of your project; Composer autoload is included by default; to add more:
    $parameters->set(Option::AUTOLOAD_PATHS, [
        // autoload specific file
        __DIR__ . '/vendor/codeigniter4/codeigniter4/system/Test/bootstrap.php',
    ]);

    // auto import fully qualified class names
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::ENABLE_CACHE, true);
    $parameters->set(Option::PHP_VERSION_FEATURES, PhpVersion::PHP_73);
    $parameters->set(Option::PHPSTAN_FOR_RECTOR_PATH, __DIR__ . '/phpstan.neon');

    // Define what rule sets will be applied
    $parameters->set(Option::SETS, [
        SetList::DEAD_CODE,
    ]);

    // get services (needed for register a single rule)
    $services = $containerConfigurator->services();

    // register a single rule
    $services->set(UnderscoreToCamelCaseVariableNameRector::class);
    $services->set(UnderscoreToCamelCaseLocalVariableNameRector::class);
};
