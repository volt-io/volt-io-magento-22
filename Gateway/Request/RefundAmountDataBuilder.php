<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Volt\Payment\Gateway\SubjectReader;

class RefundAmountDataBuilder implements BuilderInterface
{
    /**
     * Is the amount you want to refund (in minor units).
     */
    public const AMOUNT = 'amount';

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
        return [
            self::AMOUNT => $this->prepareAmount($buildSubject)
        ];
    }

    /**
     * Get amount in minor units.
     *
     * @param array $buildSubject
     * @return int
     */
    private function prepareAmount(array $buildSubject): int
    {
        $amount = $this->subjectReader->readAmount($buildSubject);

        return (int) round($amount * 100);
    }
}
