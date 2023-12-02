<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Action\Export;

use Magento\Framework\Exception\InputException;
use HenriqueAmrl\Lgpd\Api\Data\ActionContextInterface;
use HenriqueAmrl\Lgpd\Api\Data\ActionResultInterface;
use HenriqueAmrl\Lgpd\Api\ExportEntityManagementInterface;
use HenriqueAmrl\Lgpd\Model\Action\AbstractAction;
use HenriqueAmrl\Lgpd\Model\Action\ArgumentReader as ActionArgumentReader;
use HenriqueAmrl\Lgpd\Model\Action\ResultBuilder;
use function array_reduce;

final class CreateAction extends AbstractAction
{
    private ExportEntityManagementInterface $exporter;

    public function __construct(
        ResultBuilder $resultBuilder,
        ExportEntityManagementInterface $exporter
    ) {
        $this->exporter = $exporter;
        parent::__construct($resultBuilder);
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        return $this->createActionResult(
            [
                ArgumentReader::EXPORT_ENTITY => $this->exporter->create(
                    ...$this->getArguments($actionContext)
                ),
            ]
        );
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

        return [$entityId, $entityType, ArgumentReader::getFileName($actionContext)];
    }
}
