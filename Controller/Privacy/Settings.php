<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Controller\Privacy;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use HenriqueAmrl\Lgpd\Controller\AbstractPrivacy;

class Settings extends AbstractPrivacy implements HttpGetActionInterface
{
    protected function executeAction()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
