<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Http\Client\Adapters;

use Magento\Framework\ObjectManagerInterface;

class LaminasAdapter implements AdapterInterface
{
    /** @var \Magento\Framework\HTTP\LaminasClient */
    protected $client;

    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        // We need to get by object manager to be compatible with Magento 2.3
        $this->client = $objectManager->get('Magento\\Framework\\HTTP\\LaminasClient');

        // We cannot use \Magento\Framework\HTTP\Adapter\Curl::class - which has problem with headers params.
        $this->client->setOptions([
            'adapter' => 'Laminas\\Http\\Client\\Adapter\\Curl',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $config): AdapterInterface
    {
        $this->client->setOptions($config);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMethod(string $method): AdapterInterface
    {
        $this->client->setMethod($method);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHeaders(array $headers): AdapterInterface
    {
        $this->client->setHeaders($headers);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUrlEncodeBody(bool $flag): AdapterInterface
    {
        $this->client->setUrlEncodeBody($flag);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUri(string $uri): AdapterInterface
    {
        $this->client->setUri($uri);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setParameterGet(array $params): AdapterInterface
    {
        $this->client->setParameterGet($params);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setParameterPost(array $params): AdapterInterface
    {
        $this->client->setParameterPost($params);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setRawBody(string $body, string $encType): AdapterInterface
    {
        $this->client
            ->setRawBody($body)
            ->setEncType($encType);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function send()
    {
        return $this->client->send();
    }
}
