<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

class SubjectReader
{
    /**
     * Reads payment from subject
     *
     * @param array $subject
     * @return PaymentDataObjectInterface
     */
    public function readPayment(array $subject): PaymentDataObjectInterface
    {
        return \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($subject);
    }

    /**
     * Reads amount from subject
     *
     * @param array $subject
     * @return float
     */
    public function readAmount(array $subject): float
    {
        return (float) \Magento\Payment\Gateway\Helper\SubjectReader::readAmount($subject);
    }

    /**
     * Read payment ID from subject
     *
     * @param array $subject
     * @return string
     */
    public function readOrderIncrementId(array $subject): string
    {
        $payment = $this->readPayment($subject)->getPayment();
        return $payment->getOrder()->getIncrementId();
    }

    /**
     * Reads order id from subject
     *
     * @param array $subject
     * @return int
     */
    public function readOrderStoreId(array $subject): int
    {
        $payment = $this->readPayment($subject);
        return (int) $payment->getOrder()->getStoreId();
    }

    /**
     * Reads response from subject
     *
     * @param array $subject
     * @return array
     */
    public static function readResponse(array $subject): array
    {
        if (!isset($subject['response']) || !is_array($subject['response'])) {
            throw new \InvalidArgumentException('Response does not exist');
        }

        return $subject['response'];
    }
}
