<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Cron;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use HenriqueAmrl\Lgpd\Api\Data\ExportEntityInterface;
use HenriqueAmrl\Lgpd\Api\ExportEntityManagementInterface;
use HenriqueAmrl\Lgpd\Api\ExportEntityRepositoryInterface;
use HenriqueAmrl\Lgpd\Model\Config;
use Psr\Log\LoggerInterface;

/**
 * Export all scheduled entities
 */
final class ExportEntity
{
    private LoggerInterface $logger;

    private Config $config;

    private ExportEntityRepositoryInterface $exportRepository;

    private ExportEntityManagementInterface $exportManagement;

    private SearchCriteriaBuilder $criteriaBuilder;

    public function __construct(
        LoggerInterface $logger,
        Config $config,
        ExportEntityRepositoryInterface $exportRepository,
        ExportEntityManagementInterface $exportManagement,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->exportRepository = $exportRepository;
        $this->exportManagement = $exportManagement;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    public function execute(): void
    {
        if ($this->config->isModuleEnabled() && $this->config->isExportEnabled()) {
            $this->criteriaBuilder->addFilter(ExportEntityInterface::EXPORTED_AT, true, 'null');
            $this->criteriaBuilder->addFilter(ExportEntityInterface::FILE_PATH, true, 'null');

            try {
                $exportList = $this->exportRepository->getList($this->criteriaBuilder->create());

                foreach ($exportList->getItems() as $exportEntity) {
                    try {
                        $this->exportManagement->export($exportEntity);
                    } catch (NoSuchEntityException $e) {
                        $this->logger->error($e->getLogMessage(), $e->getTrace());
                        $this->exportRepository->delete($exportEntity);
                    }
                }
            } catch (Exception $e) {
                $this->logger->critical($e->getMessage(), $e->getTrace());
            }
        }
    }
}
