<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Controller\Adminhtml\Privacy;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use HenriqueAmrl\Lgpd\Api\ActionInterface;
use HenriqueAmrl\Lgpd\Controller\Adminhtml\AbstractAction;
use HenriqueAmrl\Lgpd\Model\Action\ArgumentReader;
use HenriqueAmrl\Lgpd\Model\Action\ContextBuilder;
use HenriqueAmrl\Lgpd\Model\Config;

class Erase extends AbstractAction implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'HenriqueAmrl_Lgpd::customer_erase';

    private ActionInterface $action;

    private ContextBuilder $actionContextBuilder;

    public function __construct(
        Context $context,
        Config $config,
        ActionInterface $action,
        ContextBuilder $actionContextBuilder
    ) {
        $this->action = $action;
        $this->actionContextBuilder = $actionContextBuilder;
        parent::__construct($context, $config);
    }

    protected function executeAction()
    {
        $this->actionContextBuilder->setParameters([
            ArgumentReader::ENTITY_ID => (int) $this->getRequest()->getParam('id'),
            ArgumentReader::ENTITY_TYPE => 'customer'
        ]);

        try {
            $this->action->execute($this->actionContextBuilder->create());
            $this->messageManager->addSuccessMessage(new Phrase('You erased the customer.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('customer/index');
    }
}
