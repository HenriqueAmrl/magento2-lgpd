<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Action;

use DateTime;
use Magento\Framework\Exception\LocalizedException;
use HenriqueAmrl\Lgpd\Api\ActionEntityRepositoryInterface;
use HenriqueAmrl\Lgpd\Api\ActionInterface;
use HenriqueAmrl\Lgpd\Api\Data\ActionContextInterface;
use HenriqueAmrl\Lgpd\Api\Data\ActionEntityInterface;
use HenriqueAmrl\Lgpd\Api\Data\ActionResultInterface;
use HenriqueAmrl\Lgpd\Model\ActionEntityBuilder;
use function array_merge;

final class ActionComposite implements ActionInterface
{
    private string $type;

    /**
     * @var ActionInterface[]
     */
    private array $actions;

    private ContextBuilder $contextBuilder;

    /**
     * @var ActionEntityBuilder
     */
    private ActionEntityBuilder $actionEntityBuilder;

    /**
     * @var ResultBuilder
     */
    private ResultBuilder $resultBuilder;

    private ActionEntityRepositoryInterface $actionRepository;

    public function __construct(
        string $type,
        array $actions,
        ContextBuilder $contextBuilder,
        ActionEntityBuilder $actionEntityBuilder,
        ResultBuilder $resultBuilder,
        ActionEntityRepositoryInterface $actionRepository
    ) {
        $this->type = $type;
        $this->actions = $actions;
        $this->contextBuilder = $contextBuilder;
        $this->actionEntityBuilder = $actionEntityBuilder;
        $this->resultBuilder = $resultBuilder;
        $this->actionRepository = $actionRepository;
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        $this->actionEntityBuilder->setType($this->type);
        $this->actionEntityBuilder->setParameters($actionContext->getParameters());
        $this->actionEntityBuilder->setPerformedFrom($actionContext->getPerformedFrom());
        $this->actionEntityBuilder->setPerformedBy($actionContext->getPerformedBy());
        $this->actionEntityBuilder->setPerformedAt(new DateTime());

        try {
            $this->actionEntityBuilder->setState(ActionEntityInterface::STATE_SUCCEEDED);
            $this->actionEntityBuilder->setResult($this->process($actionContext));
        } catch (LocalizedException $e) {
            $this->actionEntityBuilder->setState(ActionEntityInterface::STATE_FAILED);
            $this->actionEntityBuilder->setMessage($e->getMessage());
        }

        $result = $this->result($this->actionRepository->save($this->actionEntityBuilder->create()));

        if (isset($e)) {
            throw $e;
        }

        return $result;
    }

    /**
     * @param ActionContextInterface $actionContext
     * @return array
     * @throws LocalizedException
     */
    private function process(ActionContextInterface $actionContext): array
    {
        foreach ($this->actions as $action) {
            $this->contextBuilder->setPerformedFrom($actionContext->getPerformedFrom());
            $this->contextBuilder->setPerformedBy($actionContext->getPerformedBy());
            $this->contextBuilder->setParameters(
                array_merge($actionContext->getParameters(), $action->execute($actionContext)->getResult())
            );
            $actionContext = $this->contextBuilder->create();
        }

        return $actionContext->getParameters();
    }

    private function result(ActionEntityInterface $actionEntity): ActionResultInterface
    {
        $this->resultBuilder->setPerformedAt(new DateTime());
        $this->resultBuilder->setState($actionEntity->getState());
        $this->resultBuilder->setMessage($actionEntity->getMessage());
        $this->resultBuilder->setResult($actionEntity->getResult());

        return $this->resultBuilder->create();
    }
}
