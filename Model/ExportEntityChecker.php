<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use HenriqueAmrl\Lgpd\Api\ExportEntityCheckerInterface;
use HenriqueAmrl\Lgpd\Api\ExportEntityRepositoryInterface;

final class ExportEntityChecker implements ExportEntityCheckerInterface
{
    private ExportEntityRepositoryInterface $exportRepository;

    public function __construct(
        ExportEntityRepositoryInterface $exportRepository
    ) {
        $this->exportRepository = $exportRepository;
    }

    public function exists(int $entityId, string $entityType): bool
    {
        try {
            return (bool) $this->exportRepository->getByEntity($entityId, $entityType)->getExportId();
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    public function isExported(int $entityId, string $entityType): bool
    {
        try {
            $entity = $this->exportRepository->getByEntity($entityId, $entityType);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return $entity->getExportedAt() !== null && $entity->getFilePath() !== null;
    }
}
