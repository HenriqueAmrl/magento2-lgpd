<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Service\Erase;

/**
 * @api
 */
interface ProcessorInterface
{
    public function execute(int $entityId): bool;
}
