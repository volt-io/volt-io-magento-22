<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Request;

use Magento\Framework\Locale\Resolver;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;
use Volt\Payment\Gateway\SubjectReader;

class LanguageDataBuilder implements BuilderInterface
{
    /**
     * English (“en”) is the default language on our checkout and confirmation pages. If you would prefer to localise these pages, add the language parameter and the appropriate language code to the checkout URL.
     */
    public const LANGUAGE = 'language';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var Resolver
     */
    private $locale;

    public function __construct(
        SubjectReader $subjectReader,
        Resolver $locale
    ) {
        $this->subjectReader = $subjectReader;
        $this->locale = $locale;
    }

    public function build(array $buildSubject): array
    {
        $payment = $this->subjectReader->readPayment($buildSubject);
        if ($payment->getPayment() instanceof Payment) {
            $language = $this->locale->getLocale();

            /**
             * @var Payment $orderPayment
             */
            $orderPayment = $payment->getPayment();
            $orderPayment->setAdditionalInformation(self::LANGUAGE, $this->mapLanguage($language));
        }

        return [];
    }

    private function mapLanguage(string $language): string
    {
        // Only exception for brazilian portuguese
        if ($language === 'pt_BR') {
            return 'pt-BR';
        }

        return strstr($language, '_', true);
    }
}
