<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Volt\Payment\Gateway\Request\CountryDataBuilder;
use Volt\Payment\Gateway\Request\LanguageDataBuilder;
use Volt\Payment\Gateway\SubjectReader;

class RedirectUrlHandler implements HandlerInterface
{
    /**
     * @var string Redirect URL key
     */
    public const REDIRECT_URL = 'redirectUrl';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * RedirectUrlHandler constructor.
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        SubjectReader $subjectReader
    ) {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @throws LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        $payment = $this->subjectReader->readPayment($handlingSubject);

        if ($payment->getPayment() instanceof Payment) {
            /**
             * @var Payment $orderPayment
             */
            $orderPayment = $payment->getPayment();
            $orderPayment->setAdditionalInformation(
                self::REDIRECT_URL,
                $this->createUrl($response['checkoutUrl'], $orderPayment)
            );
        }
    }

    protected function createUrl(
        string $checkoutUrl,
        Payment $payment
    ): string {
        $language = $payment->getAdditionalInformation(LanguageDataBuilder::LANGUAGE);
        $country = $payment->getAdditionalInformation(CountryDataBuilder::COUNTRY);

        $params = [];
        if ($language) {
            $params['language'] = $language;
        }
        if ($country) {
            $params['country'] = $country;
        }

        return $checkoutUrl . '?' . http_build_query($params);
    }
}
