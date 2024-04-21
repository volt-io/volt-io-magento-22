<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Command;

use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order;

class PaymentReceived extends AbstractPaymentCommand
{
    /**
     * Process payment.
     *
     * @param TransactionInterface $transaction
     * @param OrderPaymentInterface $payment
     * @param array $commandSubject
     * @return void
     */
    public function process(
        TransactionInterface $transaction,
        OrderPaymentInterface $payment,
        array $commandSubject
    ): void {
        $order = $payment->getOrder();
        $comment = $this->getComment($commandSubject);

        if (! $transaction->getIsClosed()) {
            $amount = round($commandSubject[self::KEY_AMOUNT] / 100, 2);
            $status = $this->config->getStatusReceived((int) $order->getStoreId());
            $state = $this->getStateForStatus->execute($status, Order::STATE_PROCESSING);

            $transaction->setIsClosed(true);

            $payment
                ->setTransactionId($transaction->getTxnId())
                ->setIsTransactionApproved(true)
                ->setIsTransactionClosed(true)
                ->setIsTransactionPending(false)
                ->setShouldCloseParentTransaction(true);

            $payment->registerCaptureNotification($amount);

            $order
                ->setState($state)
                ->addStatusToHistory($status, $comment);
        } else {
            $order
                ->addCommentToStatusHistory($comment)
                ->setIsCustomerNotified(false);
        }

        $this->orderRepository->save($order);
    }
}
