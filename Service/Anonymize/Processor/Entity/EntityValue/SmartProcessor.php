<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Service\Anonymize\Processor\Entity\EntityValue;

use HenriqueAmrl\Lgpd\Model\Entity\DocumentInterface;
use HenriqueAmrl\Lgpd\Model\Entity\EntityValueProcessorInterface;
use HenriqueAmrl\Lgpd\Service\Anonymize\AnonymizerFactory;
use HenriqueAmrl\Lgpd\Service\Anonymize\MetadataInterface;
use function in_array;

final class SmartProcessor implements EntityValueProcessorInterface
{
    /**
     * @var DocumentInterface
     */
    private DocumentInterface $document;

    private MetadataInterface $metadata;

    /**
     * @var AnonymizerFactory
     */
    private AnonymizerFactory $anonymizerFactory;

    public function __construct(
        DocumentInterface $document,
        MetadataInterface $metadata,
        AnonymizerFactory $anonymizerFactory
    ) {
        $this->document = $document;
        $this->metadata = $metadata;
        $this->anonymizerFactory = $anonymizerFactory;
    }

    public function process(string $key, $value): void
    {
        if (in_array($key, $this->metadata->getAttributes(), true)) {
            $this->document->addData(
                $key,
                $this->anonymizerFactory->get(
                    $this->metadata->getAnonymizerStrategiesByAttributes()[$key] ?? AnonymizerFactory::DEFAULT_KEY
                )->anonymize($value)
            );
        }
    }
}
