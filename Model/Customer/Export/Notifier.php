<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Customer\Export;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use HenriqueAmrl\Lgpd\Api\Data\ExportEntityInterface;
use HenriqueAmrl\Lgpd\Model\Customer\Notifier\SenderInterface;
use HenriqueAmrl\Lgpd\Model\Export\NotifierInterface;
use Psr\Log\LoggerInterface;

final class Notifier implements NotifierInterface
{
    /** @var SenderInterface[] */
    private array $senders;

    private CustomerRepositoryInterface $customerRepository;

    private LoggerInterface $logger;

    public function __construct(
        array $senders,
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface $logger
    ) {
        $this->senders = $senders;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function notify(ExportEntityInterface $exportEntity): void
    {
        $customer = $this->customerRepository->getById($exportEntity->getEntityId());

        foreach ($this->senders as $sender) {
            try {
                $sender->send($customer);
            } catch (LocalizedException $e) {
                $this->logger->error($e->getLogMessage(), $e->getTrace());
            }
        }
    }
}
