<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Action\Erase;

use Magento\Framework\Exception\InputException;
use HenriqueAmrl\Lgpd\Api\Data\ActionContextInterface;
use HenriqueAmrl\Lgpd\Api\Data\ActionResultInterface;
use HenriqueAmrl\Lgpd\Api\EraseEntityManagementInterface;
use HenriqueAmrl\Lgpd\Model\Action\AbstractAction;
use HenriqueAmrl\Lgpd\Model\Action\ResultBuilder;

final class ExecuteAction extends AbstractAction
{
    private EraseEntityManagementInterface $eraseManagement;

    public function __construct(
        ResultBuilder $resultBuilder,
        EraseEntityManagementInterface $eraseManagement
    ) {
        $this->eraseManagement = $eraseManagement;
        parent::__construct($resultBuilder);
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        $eraseEntity = ArgumentReader::getEntity($actionContext);

        if ($eraseEntity === null) {
            throw InputException::requiredField('entity');
        }

        return $this->createActionResult(
            [ArgumentReader::ERASE_ENTITY => $this->eraseManagement->process($eraseEntity)]
        );
    }
}
