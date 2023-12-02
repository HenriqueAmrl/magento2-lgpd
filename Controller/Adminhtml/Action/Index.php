<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Controller\Adminhtml\Action;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Phrase;

class Index extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'HenriqueAmrl_Lgpd::lgpd_action';

    public function execute(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('HenriqueAmrl_Lgpd::lgpd_actions');
        $resultPage->getConfig()->getTitle()->set(new Phrase('View Actions'));
        $resultPage->addBreadcrumb(new Phrase('LGPD'), new Phrase('LGPD'));
        $resultPage->addBreadcrumb(new Phrase('View Actions'), new Phrase('View Actions'));

        return $resultPage;
    }
}
