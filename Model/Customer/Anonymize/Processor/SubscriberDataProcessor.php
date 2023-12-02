<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Customer\Anonymize\Processor;

use Exception;
use Magento\Newsletter\Model\ResourceModel\Subscriber as ResourceSubscriber;
use HenriqueAmrl\Lgpd\Model\Newsletter\Subscriber;
use HenriqueAmrl\Lgpd\Model\Newsletter\SubscriberFactory;
use HenriqueAmrl\Lgpd\Service\Anonymize\AnonymizerInterface;
use HenriqueAmrl\Lgpd\Service\Erase\ProcessorInterface;

final class SubscriberDataProcessor implements ProcessorInterface
{
    private AnonymizerInterface $anonymizer;

    private SubscriberFactory $subscriberFactory;

    private ResourceSubscriber $subscriberResource;

    public function __construct(
        AnonymizerInterface $anonymizer,
        SubscriberFactory $subscriberFactory,
        ResourceSubscriber $subscriberResource
    ) {
        $this->anonymizer = $anonymizer;
        $this->subscriberFactory = $subscriberFactory;
        $this->subscriberResource = $subscriberResource;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(int $customerId): bool
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber->loadByCustomerId($customerId);
        $this->anonymizer->anonymize($subscriber);
        $this->subscriberResource->save($subscriber->getRealSubscriber());

        return true;
    }
}
