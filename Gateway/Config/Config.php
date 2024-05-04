<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config as OrderConfig;

class Config extends \Magento\Payment\Gateway\Config\Config
{
    const KEY_ACTIVE = 'active';
    const KEY_TITLE = 'title';
    const KEY_SANDBOX = 'sandbox';
    const KEY_CLIENT_ID = 'client_id';
    const KEY_CLIENT_SECRET = 'client_secret';
    const KEY_NOTIFICATION_SECRET = 'notification_secret';
    const KEY_USERNAME = 'username';
    const KEY_PASSWORD = 'password';
    const KEY_PRODUCTION_URL = 'production_url';
    const KEY_SANDBOX_URL = 'sandbox_url';
    const KEY_STATUS_PENDING = 'status_pending';
    const KEY_STATUS_RECEIVED = 'status_received';
    const KEY_STATUS_FAILED = 'status_failed';

    /** @var OrderConfig */
    private $orderConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        OrderConfig $orderConfig,
        $methodCode = null,
        $pathPattern = \Magento\Payment\Gateway\Config\Config::DEFAULT_PATH_PATTERN
    ) {
        $this->orderConfig = $orderConfig;
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
    }

    /**
     * Returns whether the payment method is active
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isActive(int $storeId = null): bool
    {
        return (bool)$this->getValue(self::KEY_ACTIVE, $storeId);
    }

    /**
     * Get the title of the payment method
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTitle(int $storeId = null): string
    {
        return (string)$this->getValue(self::KEY_TITLE, $storeId);
    }

    /**
     * Returns whether the payment method is in sandbox mode
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isSandbox(int $storeId = null): bool
    {
        return (bool)$this->getValue(self::KEY_SANDBOX, $storeId);
    }

    /**
     * Get the client id
     *
     * @param int|null $storeId
     * @return string
     */
    public function getClientId(int $storeId = null): string
    {
        return (string)$this->getValue(self::KEY_CLIENT_ID, $storeId);
    }

    /**
     * Get the client secret
     *
     * @param int|null $storeId
     * @return string
     */
    public function getClientSecret(int $storeId = null): string
    {
        return (string)$this->getValue(self::KEY_CLIENT_SECRET, $storeId);
    }

    /**
     * Get the notification secret used to verify notifications
     *
     * @param int|null $storeId
     * @return string
     */
    public function getNotificationSecret(int $storeId = null): string
    {
        return (string)$this->getValue(self::KEY_NOTIFICATION_SECRET, $storeId);
    }

    /**
     * Get the username
     *
     * @param int|null $storeId
     * @return string
     */
    public function getUsername(int $storeId = null): string
    {
        return (string)$this->getValue(self::KEY_USERNAME, $storeId);
    }

    /**
     * Get the password
     *
     * @param int|null $storeId
     * @return string
     */
    public function getPassword(int $storeId = null): string
    {
        return (string)$this->getValue(self::KEY_PASSWORD, $storeId);
    }

    /**
     * Get the production url
     *
     * @param int|null $storeId
     * @return string
     */
    public function getProductionUrl(int $storeId = null): string
    {
        return (string)$this->getValue(self::KEY_PRODUCTION_URL, $storeId);
    }

    /**
     * Get the sandbox url
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSandboxUrl(int $storeId = null): string
    {
        return (string)$this->getValue(self::KEY_SANDBOX_URL, $storeId);
    }

    /**
     * Get the order status for pending payment
     *
     * @param int|null $storeId
     * @return string
     */
    public function getStatusPending(int $storeId = null): string
    {
        return (string)$this->getValue(self::KEY_STATUS_PENDING, $storeId)
            ?? $this->orderConfig->getStateDefaultStatus(Order::STATE_PENDING_PAYMENT);
    }

    /**
     * Get the order status for received payment
     *
     * @param int|null $storeId
     * @return string
     */
    public function getStatusReceived(int $storeId = null): string
    {
        return (string)$this->getValue(self::KEY_STATUS_RECEIVED, $storeId)
            ?? $this->orderConfig->getStateDefaultStatus(Order::STATE_PROCESSING);
    }

    /**
     * Get the order status for failed payment
     *
     * @param int|null $storeId
     * @return string
     */
    public function getStatusFailed(int $storeId = null): string
    {
        return (string)$this->getValue(self::KEY_STATUS_FAILED, $storeId)
            ?? $this->orderConfig->getStateDefaultStatus(Order::STATE_CANCELED);
    }
}

