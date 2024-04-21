<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Command;

use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order;

class PaymentFailed extends AbstractPaymentCommand
{
    /** Key for detailed status (if exists). */
    private const DETAILED_STATUS_KEY = 'detailedStatus';

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

        if ($order->canCancel() && ! $transaction->getIsClosed()) {
            $order->cancel();

            $status = $this->config->getStatusFailed((int) $order->getStoreId());
            $state = $this->getStateForStatus->execute($status, Order::STATE_CANCELED);

            $payment
                ->setTransactionId($transaction->getTxnId())
                ->setIsTransactionPending(false)
                ->setIsTransactionClosed(false);

            $order
                ->addStatusToHistory($status, $comment, false)
                ->setState($state)
                ->setIsCustomerNotified(false);
        } else {
            $order
                ->addCommentToStatusHistory($comment)
                ->setIsCustomerNotified(false);
        }

        $this->orderRepository->save($order);
    }

    protected function getComment(array $commandSubject): string
    {
        $comment = parent::getComment($commandSubject);

        $additionalInfo = [];
        if (isset($commandSubject[self::DETAILED_STATUS_KEY])) {
            $additionalInfo['Detailed status'] = $commandSubject[self::DETAILED_STATUS_KEY];
        }

        foreach ($additionalInfo as $key => $value) {
            $comment .= ' - ' . $key . ': ' . $value;
        }
        return $comment;
    }
}
