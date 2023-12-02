<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Service\Export\Processor\Entity\EntityValue;

use HenriqueAmrl\Lgpd\Model\Entity\DocumentInterface;
use HenriqueAmrl\Lgpd\Model\Entity\EntityValueProcessorInterface;
use HenriqueAmrl\Lgpd\Model\Entity\MetadataInterface;
use function in_array;

final class DataProcessor implements EntityValueProcessorInterface
{
    /**
     * @var DocumentInterface
     */
    public DocumentInterface $document;

    private MetadataInterface $metadata;

    public function __construct(
        DocumentInterface $document,
        MetadataInterface $metadata
    ) {
        $this->document = $document;
        $this->metadata = $metadata;
    }

    public function process(string $key, $value): void
    {
        if (in_array($key, $this->metadata->getAttributes(), true)) {
            $this->document->addData($key, $value);
        }
    }
}
