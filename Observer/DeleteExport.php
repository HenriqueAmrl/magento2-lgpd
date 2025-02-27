<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Observer;

use Exception;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use HenriqueAmrl\Lgpd\Api\Data\ExportEntityInterface;
use HenriqueAmrl\Lgpd\Api\Data\ExportEntitySearchResultsInterface;
use HenriqueAmrl\Lgpd\Api\ExportEntityRepositoryInterface;
use HenriqueAmrl\Lgpd\Model\Entity\EntityTypeResolver;
use Psr\Log\LoggerInterface;

final class DeleteExport implements ObserverInterface
{
    private ExportEntityRepositoryInterface $exportRepository;

    private SearchCriteriaBuilder $criteriaBuilder;

    private FilterBuilder $filterBuilder;

    private EntityTypeResolver $entityTypeResolver;

    private LoggerInterface $logger;

    public function __construct(
        ExportEntityRepositoryInterface $exportRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        FilterBuilder $filterBuilder,
        EntityTypeResolver $entityTypeResolver,
        LoggerInterface $logger
    ) {
        $this->exportRepository = $exportRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->entityTypeResolver = $entityTypeResolver;
        $this->logger = $logger;
    }

    public function execute(Observer $observer): void
    {
        /** @var DataObject $entity */
        $entity = $observer->getData('data_object');

        if ($entity instanceof DataObject) {
            try {
                foreach ($this->fetchExportEntities($entity)->getItems() as $exportEntity) {
                    $this->exportRepository->delete($exportEntity);
                }
            } catch (LocalizedException $e) {
                $this->logger->error($e->getLogMessage(), $e->getTrace());
            } catch (Exception $e) {
                $this->logger->error($e->getMessage(), $e->getTrace());
            }
        }
    }

    /**
     * @param DataObject $entity
     * @return ExportEntitySearchResultsInterface
     * @throws LocalizedException
     * @throws Exception
     */
    private function fetchExportEntities(DataObject $entity): SearchResultsInterface
    {
        $entityTypes = $this->entityTypeResolver->resolve($entity);

        foreach ($entityTypes as $entityType => $idFieldName) {
            $this->criteriaBuilder->addFilters([
                $this->createEntityIdFilter((int) $entity->getData($idFieldName)),
                $this->createEntityTypeFilter($entityType)
            ]);
        }

        return $this->exportRepository->getList($this->criteriaBuilder->create());
    }

    private function createEntityIdFilter(int $entityId): Filter
    {
        $this->filterBuilder->setField(ExportEntityInterface::ENTITY_ID);
        $this->filterBuilder->setValue($entityId);
        $this->filterBuilder->setConditionType('eq');

        return $this->filterBuilder->create();
    }

    private function createEntityTypeFilter(string $entityType): Filter
    {
        $this->filterBuilder->setField(ExportEntityInterface::ENTITY_TYPE);
        $this->filterBuilder->setValue($entityType);
        $this->filterBuilder->setConditionType('eq');

        return $this->filterBuilder->create();
    }
}
