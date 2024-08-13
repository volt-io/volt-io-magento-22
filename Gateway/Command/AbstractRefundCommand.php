<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Command;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;

abstract class AbstractRefundCommand extends AbstractPaymentCommand implements CommandInterface
{
    const KEY_REFUND = 'refund';
    const KEY_PAYMENT = 'payment';
    const KEY_AMOUNT = 'amount';
    const KEY_CURRENCY = 'currency';
    const KEY_STATUS = 'status';

    /**
     * Get Magento transaction by txn id.
     *
     * @param array $commandSubject
     * @return TransactionInterface|null
     * @throws NoSuchEntityException
     */
    protected function getTransaction(array $commandSubject)
    {
        $txnId = $commandSubject[self::KEY_PAYMENT];

        return $this->transactionRepository->getByTxnId($txnId);
    }

    /**
     * @param TransactionInterface $transaction
     *
     * @return OrderPaymentInterface|null
     */
    protected function getPaymentByTransaction(TransactionInterface $transaction)
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
        $comment .= ' - Refund ID: ' . $commandSubject[self::KEY_REFUND];
        $comment .= ' - Payment ID: ' . $commandSubject[self::KEY_PAYMENT];
        $comment .= ' - Amount: ' . $commandSubject[self::KEY_AMOUNT];
        $comment .= ' - Currency: ' . $commandSubject[self::KEY_CURRENCY];
        $comment .= ' - Status: ' . $commandSubject[self::KEY_STATUS];

        return $comment;
    }
}
