<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests'
    ]);

    $rectorConfig->sets(
        [
            //SetList::DEAD_CODE,
            //SetList::EARLY_RETURN,
            SetList::TYPE_DECLARATION,
            //SetList::CODE_QUALITY,
            //SetList::CODING_STYLE,
            //LevelSetList::UP_TO_PHP_83,
        ]
    );

    $rectorConfig->importNames();
    $rectorConfig->removeUnusedImports();
    $rectorConfig->importShortClasses();

    $rectorConfig->rule(RestoreDefaultNullToNullableTypePropertyRector::class);
    $rectorConfig->rule(TypedPropertyFromAssignsRector::class);
};
