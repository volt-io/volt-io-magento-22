<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Command;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Volt\Payment\Gateway\Config\Config;
use Volt\Payment\Model\GetStateForStatus;
use Volt\Payment\Model\Repository\TransactionRepository;

abstract class AbstractRefundCommand extends AbstractPaymentCommand implements CommandInterface
{
    public const KEY_REFUND_ID = 'refundId';
    public const KEY_PAYMENT_ID = 'paymentId';
    public const KEY_AMOUNT = 'amount';
    public const KEY_CURRENCY = 'currency';
    public const KEY_STATUS = 'status';

    /**
     * Get Magento transaction by txn id.
     *
     * @param array $commandSubject
     * @return TransactionInterface|null
     * @throws NoSuchEntityException
     */
    protected function getTransaction(array $commandSubject): ?TransactionInterface
    {
        $txnId = $commandSubject[self::KEY_PAYMENT_ID];

        return $this->transactionRepository->getByTxnId($txnId);
    }

    /**
     * @param TransactionInterface $transaction
     *
     * @return OrderPaymentInterface
     */
    protected function getPaymentByTransaction(TransactionInterface $transaction): ?OrderPaymentInterface
    {
        return $this->orderPaymentRepository->get($transaction->getPaymentId());
    }

    /**
     * Generate comment for status history.
     *
     * @param array $commandSubject
     * @return string
     */
    protected function getComment(array $commandSubject): string
    {
        $comment = '[Volt: Pay by Bank] Received refund notification';
        $comment .= ' - Refund ID: ' . $commandSubject[self::KEY_REFUND_ID];
        $comment .= ' - Payment ID: ' . $commandSubject[self::KEY_PAYMENT_ID];
        $comment .= ' - Amount: ' . $commandSubject[self::KEY_AMOUNT];
        $comment .= ' - Currency: ' . $commandSubject[self::KEY_CURRENCY];
        $comment .= ' - Status: ' . $commandSubject[self::KEY_STATUS];

        return $comment;
    }
}
