<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Controller\Guest;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use HenriqueAmrl\Lgpd\Api\ActionInterface;
use HenriqueAmrl\Lgpd\Controller\AbstractGuest;
use HenriqueAmrl\Lgpd\Model\Action\ArgumentReader;
use HenriqueAmrl\Lgpd\Model\Action\ContextBuilder;
use HenriqueAmrl\Lgpd\Model\Config;

class Erase extends AbstractGuest implements HttpPostActionInterface
{
    private ActionInterface $action;

    private ContextBuilder $actionContextBuilder;

    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        Config $config,
        OrderLoaderInterface $orderLoader,
        Registry $registry,
        ActionInterface $action,
        ContextBuilder $actionContextBuilder
    ) {
        $this->action = $action;
        $this->actionContextBuilder = $actionContextBuilder;
        parent::__construct($request, $resultFactory, $messageManager, $config, $orderLoader, $registry);
    }

    protected function isAllowed(): bool
    {
        return parent::isAllowed() && $this->config->isErasureEnabled();
    }

    protected function executeAction(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setRefererOrBaseUrl();

        $this->actionContextBuilder->setParameters([
            ArgumentReader::ENTITY_ID => (int) $this->currentOrder()->getEntityId(),
            ArgumentReader::ENTITY_TYPE => 'order'
        ]);

        try {
            $this->action->execute($this->actionContextBuilder->create());
            $this->messageManager->addWarningMessage(new Phrase('Your personal data is being removed soon.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        return $resultRedirect;
    }
}
