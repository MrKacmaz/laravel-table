<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;
use Rector\ValueObject\PhpVersion;
use RectorLaravel\Rector\FuncCall\AppToResolveRector;
use RectorLaravel\Rector\MethodCall\ContainerBindConcreteWithClosureOnlyRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
                   ->withCache(
                       cacheDirectory: __DIR__ . '/storage/rector/cache',
                       cacheClass: FileCacheStorage::class,
                   )
                   ->withPaths([
                       __DIR__ . '/config',
                       __DIR__ . '/src',
                       __DIR__ . '/tests',
                   ])
                   ->withPhpVersion(phpVersion: PhpVersion::PHP_85)
                   ->withImportNames()
                   ->withSets([
                       LaravelSetList::LARAVEL_120,
                       LaravelSetList::LARAVEL_CODE_QUALITY,
                       LaravelSetList::LARAVEL_COLLECTION,
                       LaravelSetList::LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME,
                       LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
                       LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
                       LaravelSetList::LARAVEL_FACTORIES,
                       LaravelSetList::LARAVEL_IF_HELPERS,
                       LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,
                       LaravelSetList::LARAVEL_TESTING,
                       LaravelSetList::LARAVEL_TYPE_DECLARATIONS,
                       PHPUnitSetList::PHPUNIT_110,
                       LevelSetList::UP_TO_PHP_85,
                       SetList::TYPE_DECLARATION,
                       SetList::EARLY_RETURN,
                   ])
                   ->withRules([
                       DeclareStrictTypesRector::class,
                   ])
                   ->withSkip([
                       AppToResolveRector::class,
                       AddOverrideAttributeToOverriddenMethodsRector::class,
                       ContainerBindConcreteWithClosureOnlyRector::class,
                   ]);
