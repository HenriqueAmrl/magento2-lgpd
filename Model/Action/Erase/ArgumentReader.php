<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Action\Erase;

use HenriqueAmrl\Lgpd\Api\Data\ActionContextInterface;
use HenriqueAmrl\Lgpd\Api\Data\EraseEntityInterface;

final class ArgumentReader
{
    public const ERASE_ENTITY = 'erase_entity';

    public static function getEntity(ActionContextInterface $actionContext): ?EraseEntityInterface
    {
        return $actionContext->getParameters()[self::ERASE_ENTITY] ?? null;
    }
}
