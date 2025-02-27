<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Service\Erase\Processor;

use HenriqueAmrl\Lgpd\Model\Config\Source\EraseComponents;
use HenriqueAmrl\Lgpd\Service\Erase\ProcessorInterface;
use HenriqueAmrl\Lgpd\Service\Erase\ProcessorResolverInterface;
use function array_column;

final class EraseProcessor implements ProcessorInterface
{
    /**
     * @var ProcessorResolverInterface
     */
    private ProcessorResolverInterface $processorResolver;

    /**
     * @var EraseComponents
     */
    private EraseComponents $eraseComponents;

    public function __construct(
        ProcessorResolverInterface $processorResolver,
        EraseComponents $eraseComponents
    ) {
        $this->processorResolver = $processorResolver;
        $this->eraseComponents = $eraseComponents;
    }

    public function execute(int $entityId): bool
    {
        $components = array_column($this->eraseComponents->toOptionArray(), 'value');

        foreach ($components as $component) {
            $processor = $this->processorResolver->resolve($component);
            if (!$processor->execute($entityId)) {
                return false;
            }
        }

        return true;
    }
}
