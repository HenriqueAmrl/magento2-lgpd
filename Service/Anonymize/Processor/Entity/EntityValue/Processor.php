<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Service\Anonymize\Processor\Entity\EntityValue;

use HenriqueAmrl\Lgpd\Model\Entity\DocumentInterface;
use HenriqueAmrl\Lgpd\Model\Entity\EntityValueProcessorInterface;
use HenriqueAmrl\Lgpd\Model\Entity\MetadataInterface;
use HenriqueAmrl\Lgpd\Service\Anonymize\AnonymizerInterface;
use function in_array;

final class Processor implements EntityValueProcessorInterface
{
    /**
     * @var DocumentInterface
     */
    private DocumentInterface $document;

    private MetadataInterface $metadata;

    private AnonymizerInterface $anonymizer;

    public function __construct(
        DocumentInterface $document,
        MetadataInterface $metadata,
        AnonymizerInterface $anonymizer
    ) {
        $this->document = $document;
        $this->metadata = $metadata;
        $this->anonymizer = $anonymizer;
    }

    public function process(string $key, $value): void
    {
        if (in_array($key, $this->metadata->getAttributes(), true)) {
            $this->document->addData($key, $this->anonymizer->anonymize($value));
        }
    }
}
