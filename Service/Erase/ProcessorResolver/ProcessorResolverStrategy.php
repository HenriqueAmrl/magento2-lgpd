<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Service\Erase\ProcessorResolver;

use HenriqueAmrl\Lgpd\Service\Erase\MetadataInterface;
use HenriqueAmrl\Lgpd\Service\Erase\ProcessorInterface;
use HenriqueAmrl\Lgpd\Service\Erase\ProcessorResolverFactory;
use HenriqueAmrl\Lgpd\Service\Erase\ProcessorResolverInterface;

final class ProcessorResolverStrategy implements ProcessorResolverInterface
{
    /**
     * @var ProcessorResolverFactory
     */
    private ProcessorResolverFactory $resolverFactory;

    private MetadataInterface $metadata;

    public function __construct(
        ProcessorResolverFactory $resolverFactory,
        MetadataInterface $metadata
    ) {
        $this->resolverFactory = $resolverFactory;
        $this->metadata = $metadata;
    }

    public function resolve(string $component): ProcessorInterface
    {
        return $this->resolverFactory->get($this->metadata->getComponentProcessor($component))->resolve($component);
    }
}
