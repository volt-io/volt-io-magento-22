<?php

declare(strict_types=1);

namespace Volt\Payment\Model\Repository;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;

class TransactionRepository
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Get transaction by txn id.
     *
     * @param string $txnId
     * @return TransactionInterface
     * @throws NoSuchEntityException
     */
    public function getByTxnId(string $txnId): TransactionInterface
    {
        $criteria = $this->searchCriteriaBuilder
            ->addFilter(TransactionInterface::TXN_ID, $txnId);

        $transactions = $this->transactionRepository->getList($criteria->create());

        if ($transactions->getTotalCount() > 0) {
            /** @var TransactionInterface $transaction */
            $transaction = $transactions->getFirstItem();

            return $transaction;
        }

        throw new NoSuchEntityException(
            __("The entity that was requested doesn't exist. Verify the entity and try again.")
        );;
    }
}
