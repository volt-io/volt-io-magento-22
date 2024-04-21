<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Psr\Log\LoggerInterface;
use Volt\Payment\Gateway\Http\TransferFactory;

class VoltClient implements ClientInterface
{
    public const GET = 'GET';
    public const POST = 'POST';
    public const PUT = 'PUT';

    /**
     * @var HttpClientFactory
     */
    private $clientFactory;

    /**
     * @var ConverterInterface | null
     */
    private $converter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * VoltClient constructor.
     *
     * @param HttpClientFactory $clientFactory
     * @param LoggerInterface $logger
     * @param ConverterInterface | null $converter
     */
    public function __construct(
        HttpClientFactory $clientFactory,
        LoggerInterface $logger,
        ConverterInterface $converter = null
    ) {
        $this->clientFactory = $clientFactory;
        $this->converter = $converter;
        $this->logger = $logger;
    }

    /**
     * {inheritdoc}
     * @param TransferInterface $transferObject
     * @return array
     * @throws ClientException
     * @throws ConverterException
     */
    public function placeRequest(TransferInterface $transferObject): array
    {
        $log = [
            'headers' => $transferObject->getHeaders(),
            'request' => $transferObject->getBody(),
            'request_uri' => $transferObject->getUri(),
            'headers' => $transferObject->getHeaders(),
        ];
        $result = [];
        $client = $this->clientFactory->create();

        try {
            $client->setOptions($transferObject->getClientConfig());
            $client->setMethod($transferObject->getMethod());
            $client->setHeaders($transferObject->getHeaders());
            $client->setUrlEncodeBody($transferObject->shouldEncode());
            $client->setUri($transferObject->getUri());

            switch ($transferObject->getMethod()) {
                case self::GET:
                    $client->setParameterGet($transferObject->getBody());
                    break;
                case self::POST:
                    $body = $transferObject->getBody();
                    if (is_array($body)) {
                        $client->setParameterPost($body);
                    } else {
                        $client->setRawBody(
                            $body,
                            $transferObject->getHeaders()['Content-Type'] ?? TransferFactory::HEADER_JSON
                        );
                    }
                    break;
                default:
                    throw new \LogicException(
                        sprintf(
                            'Unsupported HTTP method %s',
                            $transferObject->getMethod()
                        )
                    );
            }

            $response = $client->send();

            $result = $this->converter
                ? $this->converter->convert($response->getBody())
                : [$response->getBody()];

            $log['response'] = $response->getBody();
        } catch (\Zend_Http_Client_Exception|\Laminas\Http\Client\Exception\ExceptionInterface $e) {
            throw new ClientException(
                __($e->getMessage())
            );
        } finally {
            $this->logger->debug('HTTP request', $log);
        }

        return $result;
    }
}
