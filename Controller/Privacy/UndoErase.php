<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Controller\Privacy;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use HenriqueAmrl\Lgpd\Api\ActionInterface;
use HenriqueAmrl\Lgpd\Controller\AbstractPrivacy;
use HenriqueAmrl\Lgpd\Model\Action\ArgumentReader;
use HenriqueAmrl\Lgpd\Model\Action\ContextBuilder;
use HenriqueAmrl\Lgpd\Model\Config;

class UndoErase extends AbstractPrivacy implements HttpPostActionInterface
{
    private ActionInterface $action;

    private ContextBuilder $actionContextBuilder;

    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        Config $config,
        Session $customerSession,
        Http $response,
        ActionInterface $action,
        ContextBuilder $actionContextBuilder
    ) {
        $this->action = $action;
        $this->actionContextBuilder = $actionContextBuilder;
        parent::__construct($request, $resultFactory, $messageManager, $config, $customerSession, $response);
    }

    protected function isAllowed(): bool
    {
        return parent::isAllowed() && $this->config->isErasureEnabled();
    }

    protected function executeAction(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('customer/privacy/settings');

        $this->actionContextBuilder->setParameters([
            ArgumentReader::ENTITY_ID => (int) $this->customerSession->getCustomerId(),
            ArgumentReader::ENTITY_TYPE => 'customer'
        ]);

        try {
            $this->action->execute($this->actionContextBuilder->create());
            $this->messageManager->addSuccessMessage(new Phrase('You canceled your account deletion.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        return $resultRedirect;
    }
}
