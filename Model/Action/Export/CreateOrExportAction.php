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
use HenriqueAmrl\Lgpd\Model\Action\AbstractAction;
use HenriqueAmrl\Lgpd\Model\Action\ArgumentReader;
use HenriqueAmrl\Lgpd\Model\Action\Export\ArgumentReader as ExportArgumentReader;
use HenriqueAmrl\Lgpd\Model\Action\ResultBuilder;
use HenriqueAmrl\Lgpd\Model\Export\ExportEntityData;
use function array_reduce;

final class CreateOrExportAction extends AbstractAction
{
    /**
     * @var ExportEntityData
     */
    private ExportEntityData $exportEntityData;

    public function __construct(
        ResultBuilder $resultBuilder,
        ExportEntityData $exportEntityData
    ) {
        $this->exportEntityData = $exportEntityData;
        parent::__construct($resultBuilder);
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        return $this->createActionResult(
            [
                ExportArgumentReader::EXPORT_ENTITY => $this->exportEntityData->export(
                    ...$this->getArguments($actionContext)
                ),
            ]
        );
    }

    private function getArguments(ActionContextInterface $actionContext): array
    {
        $entityId = ArgumentReader::getEntityId($actionContext);
        $entityType = ArgumentReader::getEntityType($actionContext);
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
