<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Http\Client\Adapters;

use Laminas\Http\Response;

interface AdapterInterface
{
    /**
     * Set configuration parameters for this HTTP client
     *
     * @param array $config
     */
    public function setOptions(array $config): self;

    /**
     * Set the HTTP method (to the request)
     *
     * @param string $method
     * @return self
     */
    public function setMethod(string $method): self;

    /**
     * Set the headers (for the request)
     *
     * @param array $headers
     * @return self
     */
    public function setHeaders(array $headers): self;

    /**
     * Change value of internal flag to disable/enable custom prepare functionality
     *
     * @param bool $flag
     * @return self
     */
    public function setUrlEncodeBody(bool $flag): self;

    /**
     * Set Uri (to the request)
     *
     * @param string $uri
     * @return self
     */
    public function setUri(string $uri): self;

    /**
     * Set the GET parameters
     *
     * @param array $params
     * @return self
     */
    public function setParameterGet(array $params): self;

    /**
     * Set the POST parameters
     *
     * @param array $params
     * @return self
     */
    public function setParameterPost(array $params): self;

    /**
     * Set raw body (for advanced use cases).
     *
     * @param string $body
     * @param string $encType Encoding type
     * @return self
     */
    public function setRawBody(string $body, string $encType): self;

    /**
     * @return Response|\Zend_Http_Response
     */
    public function send();
}
