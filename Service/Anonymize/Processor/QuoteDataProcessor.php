<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Model\QuoteRepository;
use Magento\Quote\Model\ResourceModel\Quote\Address;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Anonymize\ProcessorInterface;

/**
 * Class QuoteDataProcessor
 */
final class QuoteDataProcessor implements ProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface
     */
    private $anonymizer;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Address
     */
    private $quoteAddressResourceModel;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface $anonymizer
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Quote\Model\ResourceModel\Quote\Address $quoteAddressResourceModel
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        AnonymizerInterface $anonymizer,
        QuoteRepository $quoteRepository,
        Address $quoteAddressResourceModel,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->anonymizer = $anonymizer;
        $this->quoteRepository = $quoteRepository;
        $this->quoteAddressResourceModel = $quoteAddressResourceModel;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute(int $customerId): bool
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id', $customerId);
        $quoteList = $this->quoteRepository->getList($searchCriteria->create());

        /** @var \Magento\Quote\Model\Quote $quote */
        foreach ($quoteList->getItems() as $quote) {
            $this->quoteRepository->save($this->anonymizer->anonymize($quote));

            /** @var \Magento\Quote\Model\Quote\Address|null $quoteAddress */
            foreach ([$quote->getBillingAddress(), $quote->getShippingAddress()] as $quoteAddress) {
                if ($quoteAddress) {
                    $this->quoteAddressResourceModel->save($this->anonymizer->anonymize($quoteAddress));
                }
            }
        }

        return true;
    }
}
