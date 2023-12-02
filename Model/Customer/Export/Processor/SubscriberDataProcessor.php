<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Customer\Export\Processor;

use HenriqueAmrl\Lgpd\Model\Entity\DataCollectorInterface;
use HenriqueAmrl\Lgpd\Model\Newsletter\Subscriber;
use HenriqueAmrl\Lgpd\Model\Newsletter\SubscriberFactory;
use HenriqueAmrl\Lgpd\Service\Export\Processor\AbstractDataProcessor;

final class SubscriberDataProcessor extends AbstractDataProcessor
{
    private SubscriberFactory $subscriberFactory;

    public function __construct(
        SubscriberFactory $subscriberFactory,
        DataCollectorInterface $dataCollector
    ) {
        $this->subscriberFactory = $subscriberFactory;
        parent::__construct($dataCollector);
    }

    public function execute(int $customerId, array $data): array
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber->loadByCustomerId($customerId);
        $data['subscriber'] = $this->collectData($subscriber);

        return $data;
    }
}
