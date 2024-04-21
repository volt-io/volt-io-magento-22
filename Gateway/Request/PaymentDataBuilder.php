<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Volt\Payment\Gateway\SubjectReader;

class PaymentDataBuilder implements BuilderInterface
{
    /**
     * A three-letter code using the internationally recognised ISO 4217 format. For example, GBP instead of pounds, USD instead of American dollar, and EUR instead of euro
     */
    public const CURRENCY_CODE = 'currencyCode';

    /**
     * This needs to be written without any decimal places. In the example, £123.45 is being requested, so is written as 12345. If the sum was £1234.50, it would be written as 123450
     */
    public const AMOUNT = 'amount';

    /**
     * This indicates what the payment is for. You choose the most appropriate code from BILL, GOODS (for physical goods only), PERSON_TO_PERSON, SERVICES or OTHER
     */
    public const TYPE = 'type';

    public const TYPE_BILL = 'BILL';

    public const TYPE_GOODS = 'GOODS';

    public const TYPE_PERSON_TO_PERSON = 'PERSON_TO_PERSON';

    public const TYPE_SERVICES = 'SERVICES';

    public const TYPE_OTHER = 'OTHER';

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
        $payment = $this->subjectReader->readPayment($buildSubject);

        $order = $payment->getOrder();

        return [
            self::CURRENCY_CODE => $order->getCurrencyCode(),
            self::AMOUNT => $this->prepareAmount($buildSubject),
            self::TYPE => self::TYPE_OTHER,
        ];
    }

    private function prepareAmount(array $buildSubject): int
    {
        $amount = $this->subjectReader->readAmount($buildSubject);

        return (int) round($amount * 100);
    }
}
