<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Erase;

use HenriqueAmrl\Lgpd\Api\Data\EraseEntityInterface;

/**
 * @api
 */
interface NotifierInterface
{
    public function notify(EraseEntityInterface $eraseEntity): void;
}
