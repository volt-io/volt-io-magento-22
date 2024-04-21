<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Command;

use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order;

class RefundFailed extends AbstractRefundCommand
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

        $order
            ->addCommentToStatusHistory($comment)
            ->setIsCustomerNotified(false);

        $this->orderRepository->save($order);
    }
}
