<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Customer\Erase;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use HenriqueAmrl\Lgpd\Api\Data\EraseEntityInterface;
use HenriqueAmrl\Lgpd\Model\Customer\Notifier\SenderInterface;
use HenriqueAmrl\Lgpd\Model\Customer\OrigDataRegistry;
use HenriqueAmrl\Lgpd\Model\Erase\NotifierInterface;
use Psr\Log\LoggerInterface;

final class Notifier implements NotifierInterface
{
    /** @var SenderInterface[] */
    private array $senders;

    private CustomerRepositoryInterface $customerRepository;

    private OrigDataRegistry $origDataRegistry;

    private LoggerInterface $logger;

    public function __construct(
        array $senders,
        CustomerRepositoryInterface $customerRepository,
        OrigDataRegistry $origDataRegistry,
        LoggerInterface $logger
    ) {
        $this->senders = $senders;
        $this->customerRepository = $customerRepository;
        $this->origDataRegistry = $origDataRegistry;
        $this->logger = $logger;
    }

    /**
     * @ingeritdoc
     * @throws LocalizedException
     */
    public function notify(EraseEntityInterface $eraseEntity): void
    {
        $customerId = $eraseEntity->getEntityId();
        $customer = $this->origDataRegistry->get($customerId) ?? $this->customerRepository->getById($customerId);

        foreach ($this->senders as $sender) {
            try {
                $sender->send($customer);
            } catch (LocalizedException $e) {
                $this->logger->error($e->getLogMessage(), $e->getTrace());
            }
        }
    }
}
