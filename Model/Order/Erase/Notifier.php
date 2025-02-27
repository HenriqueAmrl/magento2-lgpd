<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Order\Erase;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use HenriqueAmrl\Lgpd\Api\Data\EraseEntityInterface;
use HenriqueAmrl\Lgpd\Model\Erase\NotifierInterface;
use HenriqueAmrl\Lgpd\Model\Order\Notifier\SenderInterface;
use Psr\Log\LoggerInterface;

final class Notifier implements NotifierInterface
{
    /** @var SenderInterface[] */
    private array $senders;

    private OrderRepositoryInterface $orderRepository;

    private LoggerInterface $logger;

    public function __construct(
        array $senders,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger
    ) {
        $this->senders = $senders;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    public function notify(EraseEntityInterface $eraseEntity): void
    {
        $order = $this->orderRepository->get($eraseEntity->getEntityId());

        foreach ($this->senders as $sender) {
            try {
                $sender->send($order);
            } catch (LocalizedException $e) {
                $this->logger->error($e->getLogMessage(), $e->getTrace());
            }
        }
    }
}
