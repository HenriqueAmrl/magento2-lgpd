<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Api;

use Magento\Framework\Exception\LocalizedException;
use HenriqueAmrl\Lgpd\Api\Data\ActionContextInterface;
use HenriqueAmrl\Lgpd\Api\Data\ActionResultInterface;

/**
 * @api
 */
interface ActionInterface
{
    /**
     * @param ActionContextInterface $actionContext
     * @return ActionResultInterface
     * @throws LocalizedException
     */
    public function execute(ActionContextInterface $actionContext): ActionResultInterface;
}
