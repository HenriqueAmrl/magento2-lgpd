<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Block\Adminhtml\Order\Edit;

use Magento\Backend\Block\AbstractBlock;
use Magento\Backend\Block\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Sales\Block\Adminhtml\Order\View;
use HenriqueAmrl\Lgpd\Api\EraseEntityCheckerInterface;

class EraseButton extends AbstractBlock
{
    private EraseEntityCheckerInterface $eraseEntityChecker;

    public function __construct(
        Context $context,
        EraseEntityCheckerInterface $eraseEntityChecker,
        array $data = []
    ) {
        $this->eraseEntityChecker = $eraseEntityChecker;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        parent::_construct();

        /** @var View $orderView */
        $orderView = $this->getLayout()->getBlock('sales_order_edit');
        $orderId = (int) $orderView->getOrderId();

        if ($this->_authorization->isAllowed('HenriqueAmrl_Lgpd::order_erase') &&
            $this->eraseEntityChecker->canCreate($orderId, 'order')
        ) {
            $confirmMessage = new Phrase('Are you sure you want to do this?');
            $eraseUrl = $this->getUrl('sales/guest/erase', ['id' => $orderId]);

            $orderView->addButton(
                'henriqueamrl-lgpd-order-view-erase-button',
                [
                    'label' => new Phrase('Erase Personal Data'),
                    'class' => 'action-secondary erase',
                    'onclick' => 'deleteConfirm("' . $confirmMessage . '", "' . $eraseUrl . '", {"data":{}})',
                ],
                1
            );
        }
    }
}
