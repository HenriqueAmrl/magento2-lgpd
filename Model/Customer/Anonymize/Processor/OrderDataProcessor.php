<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Customer\Anonymize\Processor;

use DateTime;
use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use HenriqueAmrl\Lgpd\Api\EraseSalesInformationInterface;
use HenriqueAmrl\Lgpd\Service\Anonymize\AnonymizerInterface;
use HenriqueAmrl\Lgpd\Service\Erase\ProcessorInterface;

final class OrderDataProcessor implements ProcessorInterface
{
    private AnonymizerInterface $anonymizer;

    private OrderRepositoryInterface $orderRepository;

    private OrderAddressRepositoryInterface $addressRepository;

    private SearchCriteriaBuilder $criteriaBuilder;

    private EraseSalesInformationInterface $salesInformation;

    public function __construct(
        AnonymizerInterface $anonymizer,
        OrderRepositoryInterface $orderRepository,
        OrderAddressRepositoryInterface $addressRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        EraseSalesInformationInterface $salesInformation
    ) {
        $this->anonymizer = $anonymizer;
        $this->orderRepository = $orderRepository;
        $this->addressRepository = $addressRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->salesInformation = $salesInformation;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(int $customerId): bool
    {
        $this->criteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
        $orderList = $this->orderRepository->getList($this->criteriaBuilder->create());

        /** @var Order $order */
        foreach ($orderList->getItems() as $order) {
            $lastActive = new DateTime($order->getUpdatedAt());
            $this->salesInformation->isAlive($lastActive)
                ? $this->salesInformation->scheduleEraseEntity((int) $order->getEntityId(), 'order', $lastActive)
                : $this->anonymize($order);
        }

        return true;
    }

    private function anonymize(Order $order): void
    {
        $this->orderRepository->save($this->anonymizer->anonymize($order));

        /** @var OrderAddressInterface|null $orderAddress */
        foreach ([$order->getBillingAddress(), $order->getShippingAddress()] as $orderAddress) {
            if ($orderAddress) {
                $this->addressRepository->save($this->anonymizer->anonymize($orderAddress));
            }
        }
    }
}
