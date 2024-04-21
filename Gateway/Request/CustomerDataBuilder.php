<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Request;

use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Volt\Payment\Gateway\SubjectReader;

class CustomerDataBuilder implements BuilderInterface
{
    /**
     * Information about the shopper
     */
    public const SHOPPER = 'shopper';

    /**
     * This should uniquely identify a payer in your system,
     * and must be between three and 36 characters using numbers and letters only.
     * Required.
     */
    public const REFERENCE = 'reference';

    /**
     * A valid email address for the payer, up to 255 characters.
     * Optional.
     */
    public const EMAIL = 'email';

    /**
     * Shopper's organisation name (required when firstName and lastName not provided).
     */
    public const ORGANISATION_NAME = 'organisationName';

    /**
     * Shopper's first name (required when lastName provided).
     */
    public const FIRST_NAME = 'firstName';

    /**
     * Shopper's last name (required when firstName provided).
     */
    public const LAST_NAME = 'lastName';

    /***
     * The payer’s IP address, in IPV4 format (xxx.xxx.xxx.xxx).
     * Optional.
     */
    public const IP = 'ip';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    public function __construct(
        SubjectReader $subjectReader
    ) {
        $this->subjectReader = $subjectReader;
    }

    public function build(array $buildSubject): array
    {
        $payment = $this->subjectReader->readPayment($buildSubject);

        $order = $payment->getOrder();
        $billingAddress = $order->getBillingAddress();

        $customerId = $billingAddress->getCustomerId()
            ?? 'G' . $this->subjectReader->readOrderIncrementId($buildSubject);

        $shopper = [
            self::REFERENCE => $customerId,
            self::EMAIL => $billingAddress->getEmail(),
        ];

        return [
            self::SHOPPER => $this->injectCustomerName($shopper, $billingAddress),
        ];
    }

    protected function injectCustomerName(array $data, AddressAdapterInterface $address): array
    {
        if (!empty($address->getCompany())) {
            $data[self::ORGANISATION_NAME] = $address->getCompany();
        }

        if (!empty($address->getFirstname())) {
            $data[self::FIRST_NAME] = $address->getFirstname();
            $data[self::LAST_NAME] = $address->getLastname();
        }

        return $data;
    }
}
