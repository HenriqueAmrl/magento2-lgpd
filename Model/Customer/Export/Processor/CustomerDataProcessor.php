<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Customer\Export\Processor;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use HenriqueAmrl\Lgpd\Model\Entity\DataCollectorInterface;
use HenriqueAmrl\Lgpd\Service\Export\Processor\AbstractDataProcessor;

final class CustomerDataProcessor extends AbstractDataProcessor
{
    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        DataCollectorInterface $dataCollector
    ) {
        $this->customerRepository = $customerRepository;
        parent::__construct($dataCollector);
    }

    /**
     * @inheritdoc
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute(int $customerId, array $data): array
    {
        $data['customer'] = $this->collectData($this->customerRepository->getById($customerId));

        return $data;
    }
}
