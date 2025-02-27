<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Entity;

/**
 * @api
 */
interface DataCollectorInterface
{
    public function collect(object $entity): array;
}
