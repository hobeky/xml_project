<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/src',
    ]);

    // B. standalone rule
    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);

    $ecsConfig->sets([
        SetList::PSR_12,
        SetList::SPACES,
        SetList::CLEAN_CODE,
        SetList::STRICT,
        SetList::DOCTRINE_ANNOTATIONS,
        SetList::PHPUNIT,
        SetList::NAMESPACES
    ]);

    $ecsConfig->lineEnding("\n");
};