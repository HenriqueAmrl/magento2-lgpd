<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Controller\Adminhtml\Action;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\AbstractAggregateException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use HenriqueAmrl\Lgpd\Api\Data\ActionResultInterface;
use HenriqueAmrl\Lgpd\Model\Action\ActionFactory;
use HenriqueAmrl\Lgpd\Model\Action\ContextBuilder;
use HenriqueAmrl\Lgpd\Model\Config\Source\ActionStates;

class Execute extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'HenriqueAmrl_Lgpd::lgpd_actions_execute';

    /**
     * @var ActionFactory
     */
    private ActionFactory $actionFactory;

    private ContextBuilder $contextBuilder;

    private ActionStates $actionStates;

    public function __construct(
        Context $context,
        ActionFactory $actionFactory,
        ContextBuilder $contextBuilder,
        ActionStates $actionStates
    ) {
        $this->actionFactory = $actionFactory;
        $this->contextBuilder = $contextBuilder;
        $this->actionStates = $actionStates;
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/new');

        try {
            $result = $this->proceed($this->getRequest()->getParam('type'));
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addSuccessMessage(
                new Phrase('The action state is now: %1.', [$this->actionStates->getOptionText($result->getState())])
            );
        } catch (AbstractAggregateException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            foreach ($e->getErrors() as $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        return $resultRedirect;
    }

    /**
     * @param string $actionType
     * @return ActionResultInterface
     * @throws LocalizedException
     */
    private function proceed(string $actionType): ActionResultInterface
    {
        $parameters = [];
        /** @var array $param */
        foreach ((array) $this->getRequest()->getParam('parameters', []) as $param) {
            $parameters[$param['name']] = $param['value'];
        }
        $this->contextBuilder->setParameters($parameters);

        return $this->actionFactory->get($actionType)->execute($this->contextBuilder->create());
    }
}
