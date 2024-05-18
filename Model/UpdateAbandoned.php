<?php

declare(strict_types=1);

namespace Volt\Payment\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Volt\Payment\Model\Repository\TransactionRepository;

class UpdateAbandoned
{
    /**
     * @var int Hours after ABANDONED check.
     */
    protected const HOURS = 3;

    /**
     * @var OrderPaymentRepositoryInterface
     */
    protected $orderPaymentRepository;

    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var CancelOrder
     */
    protected $cancelOrder;

    public function __construct(
        OrderPaymentRepositoryInterface $orderPaymentRepository,
        TransactionRepository $transactionRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CancelOrder $cancelOrder
    ) {
        $this->orderPaymentRepository = $orderPaymentRepository;
        $this->transactionRepository = $transactionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cancelOrder = $cancelOrder;
    }

    /**
     * Search orders with status ABANDONED_BY_USER.
     * After receiving ABANDONED_BY_USER status, we're waiting 3 hours to change status of order to failed payment.
     * If the order is updated, return the number of updated orders.
     *
     * @return int Number of updated orders
     * @throws NoSuchEntityException
     */
    public function execute(): int
    {
        $count = 0;

        // We're using like to have match with MySQL and MariaDB JSON field.
        $this->searchCriteriaBuilder->addFilter('additional_information', '%"detailedStatus":"ABANDONED_BY_USER"%', 'like');
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $orderPayments = $this->orderPaymentRepository->getList($searchCriteria)->getItems();

        foreach ($orderPayments as $orderPayment) {
            $order = $orderPayment->getOrder();

            if ($this->shouldUpdate($order)) {
                ++$count;

                $this->cancelOrder->execute(
                    $order,
                    $orderPayment,
                    'Order was abandoned by user.'
                );
            }
        }

        return $count;
    }

    protected function shouldUpdate(OrderInterface $order): bool
    {
        // Updated as is string format.
        $updatedAt = strtotime($order->getUpdatedAt());
        $now = strtotime('now');
        $diff = $now - $updatedAt;

        return $diff > self::HOURS * 3600 && $order->canCancel();
    }
}
