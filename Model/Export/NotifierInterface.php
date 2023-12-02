<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Export;

use HenriqueAmrl\Lgpd\Api\Data\ExportEntityInterface;

/**
 * @api
 */
interface NotifierInterface
{
    public function notify(ExportEntityInterface $exportEntity): void;
}
