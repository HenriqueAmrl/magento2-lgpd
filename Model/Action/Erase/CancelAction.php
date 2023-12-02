<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Action\Erase;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use HenriqueAmrl\Lgpd\Api\Data\ActionContextInterface;
use HenriqueAmrl\Lgpd\Api\Data\ActionResultInterface;
use HenriqueAmrl\Lgpd\Api\Data\EraseEntityInterface;
use HenriqueAmrl\Lgpd\Api\EraseEntityManagementInterface;
use HenriqueAmrl\Lgpd\Api\EraseEntityRepositoryInterface;
use HenriqueAmrl\Lgpd\Model\Action\AbstractAction;
use HenriqueAmrl\Lgpd\Model\Action\ArgumentReader as ActionArgumentReader;
use HenriqueAmrl\Lgpd\Model\Action\ResultBuilder;
use function array_reduce;

final class CancelAction extends AbstractAction
{
    private EraseEntityRepositoryInterface $eraseRepository;

    private EraseEntityManagementInterface $eraseManagement;

    public function __construct(
        ResultBuilder $resultBuilder,
        EraseEntityRepositoryInterface $eraseRepository,
        EraseEntityManagementInterface $eraseManagement
    ) {
        $this->eraseRepository = $eraseRepository;
        $this->eraseManagement = $eraseManagement;
        parent::__construct($resultBuilder);
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        $arguments = $this->getArguments($actionContext);

        return $this->createActionResult(
            [
                ArgumentReader::ERASE_ENTITY => $this->resolveEntity(...$arguments),
                'canceled' => $this->eraseManagement->cancel(...$arguments),
            ]
        );
    }

    /**
     * @param int $entityId
     * @param string $entityType
     * @return EraseEntityInterface
     * @throws NoSuchEntityException
     */
    private function resolveEntity(int $entityId, string $entityType): EraseEntityInterface
    {
        return clone $this->eraseRepository->getByEntity($entityId, $entityType);
    }

    private function getArguments(ActionContextInterface $actionContext): array
    {
        $entityId = ActionArgumentReader::getEntityId($actionContext);
        $entityType = ActionArgumentReader::getEntityType($actionContext);
        $errors = [];

        if ($entityId === null) {
            $errors[] = InputException::requiredField('entity_id');
        }
        if ($entityType === null) {
            $errors[] = InputException::requiredField('entity_type');
        }
        if (!empty($errors)) {
            throw array_reduce(
                $errors,
                static function (InputException $aggregated, InputException $input): InputException {
                    return $aggregated->addException($input);
                },
                new InputException()
            );
        }

        return [$entityId, $entityType];
    }
}
