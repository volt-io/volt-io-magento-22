<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Volt\Payment\Gateway\SubjectReader;

class RefundPaymentIdDataBuilder implements BuilderInterface
{
    /**
     * Payment ID
     */
    public const PAYMENT_ID = 'payment_id';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    public function build(array $buildSubject): array
    {
        $paymentId = $this->subjectReader->readPayment($buildSubject)
            ->getPayment()
            ->getLastTransId();

        return [
            self::PAYMENT_ID => $paymentId
        ];
    }
}
