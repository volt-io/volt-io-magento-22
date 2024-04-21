<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;
use Volt\Payment\Gateway\SubjectReader;

class CountryDataBuilder implements BuilderInterface
{
    /**
     * The list of banks shown on the checkout page is determined by the country chosen, which can be set in a number of ways. If, as a merchant, you’re only configured to use one country, that country will be used.
     */
    public const COUNTRY = 'country';

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

        if ($payment->getPayment() instanceof Payment) {
            $countryCode = $order->getBillingAddress()->getCountryId();

            /**
             * @var Payment $orderPayment
             */
            $orderPayment = $payment->getPayment();
            $orderPayment->setAdditionalInformation(self::COUNTRY, $countryCode);
        }

        return [];
    }
}
