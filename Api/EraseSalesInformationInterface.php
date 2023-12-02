<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Api;

use DateTime;
use HenriqueAmrl\Lgpd\Api\Data\EraseEntityInterface;

/**
 * @api
 */
interface EraseSalesInformationInterface
{
    public function scheduleEraseEntity(int $entityId, string $entityType, DateTime $lastActive): EraseEntityInterface;

    public function isAlive(DateTime $lastActive): bool;
}
