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
use Volt\Payment\Gateway\Request\RefundPaymentIdDataBuilder;
use Volt\Payment\Gateway\Request\StoreIdDataBuilder;

class RefundTransferFactory implements TransferFactoryInterface
{
    /**
     * Path to payments API.
     */
    public const PATH = '/payments/{paymentId}/request-refund';

    /**
     * Used method (GET, POST).
     */
    public const POST = 'POST';

    /**
     * Header name constants.
     */
    public const HEADER_CONTENT_TYPE = 'Content-Type';
    public const HEADER_AUTHORIZATION = 'Authorization';
    public const HEADER_VOLT_PARTNER_ID = 'Volt-Partner-Attribution-Id';

    /**
     * Header value constants.
     */
    public const HEADER_JSON = 'application/json';
    public const VOLT_PARTNER_ID = '78884f87-0171-4937-9d3c-99f36400c4c5';

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

        $paymentId = $request[RefundPaymentIdDataBuilder::PAYMENT_ID];
        unset($request[RefundPaymentIdDataBuilder::PAYMENT_ID]);

        $uri = $this->getUrl($paymentId, $storeId);

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
     * Generate URI
     *
     * @param string $paymentId
     * @param int|null $storeId
     * @return string
     */
    protected function getUrl(string $paymentId, int $storeId = null): string
    {
        $uri = $this->config->isSandbox($storeId)
            ? $this->config->getSandboxUrl($storeId)
            : $this->config->getProductionUrl($storeId);

        return str_replace(
            '{paymentId}',
            $paymentId,
            $uri . self::PATH
        );
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
