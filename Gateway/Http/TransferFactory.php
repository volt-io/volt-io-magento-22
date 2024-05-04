<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Http;

use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Volt\Payment\Gateway\Config\Config;
use Volt\Payment\Gateway\Http\Converter\ArrayToJson;
use Volt\Payment\Gateway\Request\AuthenticationDataBuilder;
use Volt\Payment\Gateway\Request\StoreIdDataBuilder;

class TransferFactory implements TransferFactoryInterface
{
    /**
     * Path to payments API.
     */
    const PATH = '/v2/payments';

    /**
     * Used method (GET, POST).
     */
    const POST = 'POST';

    /**
     * Header name constants.
     */
    const HEADER_CONTENT_TYPE = 'Content-Type';
    const HEADER_AUTHORIZATION = 'Authorization';
    const HEADER_VOLT_PARTNER_ID = 'Volt-Partner-Attribution-Id';

    /**
     * Header value constants.
     */
    const HEADER_JSON = 'application/json';
    const VOLT_PARTNER_ID = '78884f87-0171-4937-9d3c-99f36400c4c5';

    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * @var Config $config
     */
    private $config;

    /**
     * @var ArrayToJson
     */
    private $converter;

    /**
     * @param TransferBuilder $transferBuilder
     * @param Config $config
     * @param ArrayToJson $converter
     */
    public function __construct(
        TransferBuilder $transferBuilder,
        Config $config,
        ArrayToJson $converter
    ) {
        $this->transferBuilder = $transferBuilder;
        $this->config = $config;
        $this->converter = $converter;
    }

    /**
     * Create transfer object.
     *
     * @throws ConverterException
     */
    public function create(array $request): TransferInterface
    {
        $storeId = $request[StoreIdDataBuilder::STORE_ID];
        unset($request[StoreIdDataBuilder::STORE_ID]);

        $bearerToken = $request[AuthenticationDataBuilder::BEARER_TOKEN];
        unset($request[AuthenticationDataBuilder::BEARER_TOKEN]);

        $uri = $this->getUrl($storeId);

        return $this->transferBuilder
            ->setMethod(self::POST)
            ->setUri($uri)
            ->setBody($this->prepareBody($request))
            ->shouldEncode(true)
            ->setHeaders([
                self::HEADER_CONTENT_TYPE => self::HEADER_JSON,
                self::HEADER_AUTHORIZATION => 'Bearer ' . $bearerToken,
                self::HEADER_VOLT_PARTNER_ID => self::VOLT_PARTNER_ID,
            ])
            ->build();
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    protected function getUrl(int $storeId = null): string
    {
        $uri = $this->config->isSandbox($storeId)
            ? $this->config->getSandboxUrl($storeId)
            : $this->config->getProductionUrl($storeId);

        return $uri . self::PATH;
    }

    /**
     * Prepare body for request
     *
     * @param array $request
     * @return string
     * @throws ConverterException
     */
    private function prepareBody(array $request): string
    {
        return $this->converter->convert($request);
    }
}
