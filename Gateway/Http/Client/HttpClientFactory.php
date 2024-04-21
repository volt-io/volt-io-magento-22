<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Http\Client;

use Magento\Framework\ObjectManagerInterface;
use Volt\Payment\Gateway\Http\Client\Adapters\AdapterInterface;
use Volt\Payment\Gateway\Http\Client\Adapters\LaminasAdapter;
use Volt\Payment\Gateway\Http\Client\Adapters\ZendAdapter;

class HttpClientFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters.
     * We using adapters to support both Magento 2.3 and 2.4.
     *
     * @param array $data
     * @return AdapterInterface
     */
    public function create(array $data = []): AdapterInterface
    {
        $instanceName = ZendAdapter::class;
        if (class_exists('\\Magento\\Framework\\HTTP\\LaminasClient')) {
            $instanceName = LaminasAdapter::class;
        }

        return $this->objectManager->create($instanceName, $data);
    }
}
