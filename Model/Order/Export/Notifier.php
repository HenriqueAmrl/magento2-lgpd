<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Order\Export;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use HenriqueAmrl\Lgpd\Api\Data\ExportEntityInterface;
use HenriqueAmrl\Lgpd\Model\Export\NotifierInterface;
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

    /**
     * @inheritdoc
     */
    public function notify(ExportEntityInterface $exportEntity): void
    {
        $order = $this->orderRepository->get($exportEntity->getEntityId());

        foreach ($this->senders as $sender) {
            try {
                $sender->send($order);
            } catch (LocalizedException $e) {
                $this->logger->error($e->getLogMessage(), $e->getTrace());
            }
        }
    }
}
