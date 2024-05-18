<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Command;

use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order;

class PaymentFailed extends AbstractPaymentCommand
{
    /** Key for detailed status (if exists). */
    const DETAILED_STATUS_KEY = 'detailedStatus';

    /** Value for detailed status which we need to ignore (for 3 hours). */
    const ABANDONED_BY_USER = 'ABANDONED_BY_USER';

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
    ) {
        $order = $payment->getOrder();
        $comment = $this->getComment($commandSubject);

        if (
            $order->canCancel()
            && ! $transaction->getIsClosed()
            && $this->getDetailedStatus($commandSubject) !== self::ABANDONED_BY_USER
        ) {
            $payment
                ->setTransactionId($transaction->getTxnId());

            $this->cancelOrder->execute($order, $payment, $comment);
        } else {
            $order
                ->addCommentToStatusHistory($comment)
                ->setIsCustomerNotified(false);

            $this->orderRepository->save($order);
        }
    }

    protected function getComment(array $commandSubject): string
    {
        $comment = parent::getComment($commandSubject);

        $additionalInfo = [
            'Detailed status' => $this->getDetailedStatus($commandSubject),
        ];

        foreach ($additionalInfo as $key => $value) {
            $comment .= ' - ' . $key . ': ' . $value;
        }
        return $comment;
    }
}
