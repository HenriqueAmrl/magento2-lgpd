<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use HenriqueAmrl\Lgpd\Api\ActionEntityRepositoryInterface;
use HenriqueAmrl\Lgpd\Api\Data\ActionEntityInterface;
use HenriqueAmrl\Lgpd\Api\Data\ActionEntityInterfaceFactory;
use HenriqueAmrl\Lgpd\Api\Data\ActionEntitySearchResultsInterface;
use HenriqueAmrl\Lgpd\Api\Data\ActionEntitySearchResultsInterfaceFactory;
use HenriqueAmrl\Lgpd\Model\ResourceModel\ActionEntity as ActionEntityResource;
use HenriqueAmrl\Lgpd\Model\ResourceModel\ActionEntity\Collection;
use HenriqueAmrl\Lgpd\Model\ResourceModel\ActionEntity\CollectionFactory;

final class ActionEntityRepository implements ActionEntityRepositoryInterface
{
    /**
     * @var ActionEntityResource
     */
    private ActionEntityResource $actionEntityResource;

    /**
     * @var ActionEntityInterfaceFactory
     */
    private ActionEntityInterfaceFactory $actionFactory;

    private CollectionFactory $collectionFactory;

    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var ActionEntitySearchResultsInterfaceFactory
     */
    private ActionEntitySearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var ActionEntityInterface[]
     */
    private array $instances = [];

    public function __construct(
        ActionEntityResource $actionEntityResource,
        ActionEntityInterfaceFactory $actionFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        ActionEntitySearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->actionEntityResource = $actionEntityResource;
        $this->actionFactory = $actionFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function save(ActionEntityInterface $actionEntity): ActionEntityInterface
    {
        try {
            $this->actionEntityResource->save($actionEntity);
            $this->instances[$actionEntity->getActionId()] = $actionEntity;
        } catch (Exception $e) {
            throw new CouldNotSaveException(new Phrase('Could not save the entity.'), $e);
        }

        return $actionEntity;
    }

    public function getById(int $actionId): ActionEntityInterface
    {
        if (!isset($this->instances[$actionId])) {
            /** @var ActionEntityInterface $actionEntity */
            $actionEntity = $this->actionFactory->create();
            $this->actionEntityResource->load($actionEntity, $actionId, ActionEntityInterface::ID);

            if (!$actionEntity->getActionId()) {
                throw NoSuchEntityException::singleField(ActionEntityInterface::ID, $actionId);
            }

            $this->instances[$actionId] = $actionEntity;
        }

        return $this->instances[$actionId];
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ActionEntitySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function delete(ActionEntityInterface $actionEntity): bool
    {
        try {
            unset($this->instances[$actionEntity->getActionId()]);
            $this->actionEntityResource->delete($actionEntity);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(
                new Phrase('Could not delete entity with id "%1".', [$actionEntity->getActionId()]),
                $e
            );
        }

        return true;
    }
}
