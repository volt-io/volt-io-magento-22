<?php

declare(strict_types=1);

namespace Volt\Payment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use PayU\PaymentGateway\Api\PayUConfigInterface;
use Volt\Payment\Gateway\Config\Config;

final class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string Method code
     */
    public const CODE = 'volt';

    /**
     * @var string Redirect route
     */
    private const REDIRECT_ROUTE = 'volt/payment/redirect';

    /**
     * @var string Logo source
     */
    private const LOGO_SRC = 'Volt_Payment::images/logo.svg';

    /**
     * @var Config
     */
    public $config;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * ConfigProvider constructor.
     *
     * @param Config $config
     * @param UrlInterface $url
     * @param AssetRepository $assetRepository
     */
    public function __construct(
        Config $config,
        UrlInterface $url,
        AssetRepository $assetRepository
    ) {
        $this->config = $config;
        $this->url = $url;
        $this->assetRepository = $assetRepository;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->isActive(),
                    'isSandbox' => $this->config->isSandbox(),
                    'title' => $this->config->getTitle(),
                    'redirectUrl' => $this->url->getUrl(self::REDIRECT_ROUTE),
                    'logoUrl' => $this->assetRepository->getUrl(self::LOGO_SRC),
                ]
            ]
        ];
    }
}
