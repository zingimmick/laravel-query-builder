<?php

declare(strict_types=1);

use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\AddSeeTestAnnotationRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\Privatization\Rector\MethodCall\PrivatizeLocalGetterToPropertyRector;
use Rector\Set\ValueObject\LevelSetList;
use Zing\CodingStandard\Set\RectorSetList;

return static function (Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->sets([LevelSetList::UP_TO_PHP_80, PHPUnitSetList::PHPUNIT_CODE_QUALITY, RectorSetList::CUSTOM]);
    $rectorConfig->parallel();
    $rectorConfig->skip([
        RenameVariableToMatchMethodCallReturnTypeRector::class,
        RenameParamToMatchTypeRector::class,
        AddSeeTestAnnotationRector::class,
        FinalizeClassesWithoutChildrenRector::class,
        PrivatizeLocalGetterToPropertyRector::class,
    ]);
    $rectorConfig->paths([
        __DIR__ . '/bin',
        __DIR__ . '/samples',
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/ecs.php',
        __DIR__ . '/rector.php',
    ]);
};
