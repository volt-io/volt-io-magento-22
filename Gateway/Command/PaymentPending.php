<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Command;

use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order;

class PaymentPending extends AbstractPaymentCommand
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

        if (! $transaction->getIsClosed()) {
            $status = $this->config->getStatusPending((int) $order->getStoreId());
            $state = $this->getStateForStatus->execute($status, Order::STATE_PROCESSING);

            $payment
                ->setTransactionId($transaction->getTxnId())
                ->setIsTransactionPending(true)
                ->setIsTransactionClosed(false);

            $order->setState($state)
                ->addStatusToHistory($status, $this->getComment($commandSubject));
        } else {
            $order
                ->addCommentToStatusHistory($this->getComment($commandSubject))
                ->setIsCustomerNotified(false)
            ;
        }

        $this->orderRepository->save($order);
    }
}
