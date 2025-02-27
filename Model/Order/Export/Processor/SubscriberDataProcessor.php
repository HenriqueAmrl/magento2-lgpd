<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Order\Export\Processor;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use HenriqueAmrl\Lgpd\Model\Entity\DataCollectorInterface;
use HenriqueAmrl\Lgpd\Model\Newsletter\Subscriber;
use HenriqueAmrl\Lgpd\Model\Newsletter\SubscriberFactory;

final class SubscriberDataProcessor extends AbstractDataProcessor
{
    private SubscriberFactory $subscriberFactory;

    public function __construct(
        SubscriberFactory $subscriberFactory,
        OrderRepositoryInterface $orderRepository,
        DataCollectorInterface $dataCollector
    ) {
        $this->subscriberFactory = $subscriberFactory;
        parent::__construct($orderRepository, $dataCollector);
    }

    protected function export(OrderInterface $order, array $data): array
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber->loadByEmail($order->getCustomerEmail());
        $data['subscriber'] = $this->collectData($subscriber);

        return $data;
    }
}
