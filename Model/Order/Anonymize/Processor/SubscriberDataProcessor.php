<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Order\Anonymize\Processor;

use Exception;
use Magento\Newsletter\Model\ResourceModel\Subscriber as ResourceSubscriber;
use Magento\Sales\Api\OrderRepositoryInterface;
use HenriqueAmrl\Lgpd\Model\Newsletter\Subscriber;
use HenriqueAmrl\Lgpd\Model\Newsletter\SubscriberFactory;
use HenriqueAmrl\Lgpd\Service\Anonymize\AnonymizerInterface;
use HenriqueAmrl\Lgpd\Service\Erase\ProcessorInterface;

final class SubscriberDataProcessor implements ProcessorInterface
{
    private AnonymizerInterface $anonymizer;

    private OrderRepositoryInterface $orderRepository;

    private SubscriberFactory $subscriberFactory;

    private ResourceSubscriber $subscriberResource;

    public function __construct(
        AnonymizerInterface $anonymizer,
        OrderRepositoryInterface $orderRepository,
        SubscriberFactory $subscriberFactory,
        ResourceSubscriber $subscriberResource
    ) {
        $this->anonymizer = $anonymizer;
        $this->orderRepository = $orderRepository;
        $this->subscriberFactory = $subscriberFactory;
        $this->subscriberResource = $subscriberResource;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(int $orderId): bool
    {
        $order = $this->orderRepository->get($orderId);

        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber->loadByEmail($order->getCustomerEmail());
        $this->anonymizer->anonymize($subscriber);
        $this->subscriberResource->save($subscriber->getRealSubscriber());

        return true;
    }
}
