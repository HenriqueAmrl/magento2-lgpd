<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Action\Export;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use HenriqueAmrl\Lgpd\Api\Data\ActionContextInterface;
use HenriqueAmrl\Lgpd\Api\Data\ActionResultInterface;
use HenriqueAmrl\Lgpd\Api\ExportEntityManagementInterface;
use HenriqueAmrl\Lgpd\Api\ExportEntityRepositoryInterface;
use HenriqueAmrl\Lgpd\Model\Action\AbstractAction;
use HenriqueAmrl\Lgpd\Model\Action\Export\ArgumentReader as ExportArgumentReader;
use HenriqueAmrl\Lgpd\Model\Action\ResultBuilder;

final class ExportAction extends AbstractAction
{
    private ExportEntityRepositoryInterface $exportRepository;

    private ExportEntityManagementInterface $exportManagement;

    public function __construct(
        ResultBuilder $resultBuilder,
        ExportEntityRepositoryInterface $exportRepository,
        ExportEntityManagementInterface $exportManagement
    ) {
        $this->exportRepository = $exportRepository;
        $this->exportManagement = $exportManagement;
        parent::__construct($resultBuilder);
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        $exportEntity = ArgumentReader::getEntity($actionContext);

        if ($exportEntity === null) {
            throw InputException::requiredField('entity');
        }

        try {
            $exportEntity = $this->exportManagement->export($exportEntity);
        } catch (NoSuchEntityException $e) {
            $this->exportRepository->delete($exportEntity);

            throw $e;
        }

        return $this->createActionResult(
            [ExportArgumentReader::EXPORT_ENTITY => $exportEntity]
        );
    }
}
