<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Service\Anonymize;

use HenriqueAmrl\Lgpd\Model\Entity\MetadataInterface as EntityMetadataInterface;

/**
 * @api
 */
interface MetadataInterface extends EntityMetadataInterface
{
    public function getAnonymizerStrategiesByAttributes(?string $scopeCode = null): array;
}
