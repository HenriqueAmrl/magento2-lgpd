<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Entity\EntityValue;

use HenriqueAmrl\Lgpd\Model\Entity\EntityValueProcessorInterface;

final class StrategyProcessor implements EntityValueProcessorInterface
{
    /**
     * @var EntityValueProcessorInterface[]
     */
    private array $processors;

    public function __construct(
        array $processors
    ) {
        $this->processors = $processors;
    }

    public function process(string $key, $value): void
    {
        ($this->processors[$key] ?? $this->processors['default'])->process($key, $value);
    }
}
