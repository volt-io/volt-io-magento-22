<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Http\Client\Adapters;

class ZendAdapter implements AdapterInterface
{
    /** @var \Magento\Framework\HTTP\ZendClient */
    protected $client;

    public function __construct(
        \Magento\Framework\HTTP\ZendClient $client
    ) {
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $config): AdapterInterface
    {
        $this->client->setConfig($config);
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
        // Encoding type for Zend is ignored - only header is used
        $this->client->setRawData($body);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function send(): \Zend_Http_Response
    {
        return $this->client->request();
    }
}
