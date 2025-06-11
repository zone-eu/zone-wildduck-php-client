<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveNullTagValueNodeRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\FunctionLike\MixedTypeRector;
use Rector\Php83\Rector\ClassConst\AddTypeToConstRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Transform\Rector\Class_\AddAllowDynamicPropertiesAttributeRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests'
    ]);

    $rectorConfig->sets(
        [
            SetList::DEAD_CODE,
            SetList::EARLY_RETURN,
            SetList::TYPE_DECLARATION,
            SetList::CODE_QUALITY,
            LevelSetList::UP_TO_PHP_83,
        ]
    );

    $rectorConfig->rule(RestoreDefaultNullToNullableTypePropertyRector::class);
    $rectorConfig->rule(TypedPropertyFromAssignsRector::class);
    $rectorConfig->rule(CompleteDynamicPropertiesRector::class);
    $rectorConfig->rule(AddTypeToConstRector::class);
    $rectorConfig->rule(AddAllowDynamicPropertiesAttributeRector::class);

    $rectorConfig->importNames();
    $rectorConfig->removeUnusedImports();
    $rectorConfig->importShortClasses();

	$rectorConfig->skip([
		RemoveUselessParamTagRector::class,
		RemoveUselessVarTagRector::class,
		RemoveUselessReturnTagRector::class,
		RemoveNullTagValueNodeRector::class,
		MixedTypeRector::class,
		ClassPropertyAssignToConstructorPromotionRector::class,
	]);
};
