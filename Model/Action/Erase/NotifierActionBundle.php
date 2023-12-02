<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Action\Erase;

use Magento\Framework\Exception\InputException;
use Magento\Framework\ObjectManagerInterface;
use HenriqueAmrl\Lgpd\Api\Data\ActionContextInterface;
use HenriqueAmrl\Lgpd\Api\Data\ActionResultInterface;
use HenriqueAmrl\Lgpd\Model\Action\AbstractAction;
use HenriqueAmrl\Lgpd\Model\Action\ArgumentReader as ActionArgumentReader;
use HenriqueAmrl\Lgpd\Model\Action\ResultBuilder;
use HenriqueAmrl\Lgpd\Model\Erase\NotifierInterface;

final class NotifierActionBundle extends AbstractAction
{
    /**
     * @var string[]
     */
    private array $notifiers;

    private ObjectManagerInterface $objectManager;

    public function __construct(
        ResultBuilder $resultBuilder,
        array $notifiers,
        ObjectManagerInterface $objectManager
    ) {
        $this->notifiers = $notifiers;
        $this->objectManager = $objectManager;
        parent::__construct($resultBuilder);
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        $this->resolveNotifier($actionContext)->notify(ArgumentReader::getEntity($actionContext));

        return $this->createActionResult(['is_notify' => true]);
    }

    /**
     * @param ActionContextInterface $actionContext
     * @return NotifierInterface
     * @throws InputException
     */
    private function resolveNotifier(ActionContextInterface $actionContext): NotifierInterface
    {
        $entityType = ActionArgumentReader::getEntityType($actionContext);

        if (!isset($this->notifiers[$entityType])) {
            throw InputException::invalidFieldValue('entity_type', $entityType);
        }

        return $this->objectManager->get($this->notifiers[$entityType]);
    }
}
