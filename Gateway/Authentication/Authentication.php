<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Authentication;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferInterface;
use Volt\Payment\Gateway\Config\Config;

class Authentication
{
    /**
     * Path to oAuth API.
     */
    private const PATH = '/oauth';

    /**
     * Used method (GET, POST).
     */
    private const METHOD = 'POST';

    /**
     * oAuth Grant type.
     */
    private const GRANT_TYPE = 'password';

    /**
     * Cache identifier.
     */
    private const CACHE_IDENTIFIER = 'volt_authentication';

    /**
     * Cache tags.
     */
    private const CACHE_TAGS = ['volt', 'auth'];

    /**
     * Cache lifetime in seconds.
     */
    private const CACHE_LIFETIME = 3600; // 1 hour

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * @var FrontendInterface
     */
    private $cache;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Authentication constructor.
     *
     * @param Config $config
     * @param ClientInterface $client
     * @param TransferBuilder $transferBuilder
     * @param FrontendInterface $cache
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Config $config,
        ClientInterface $client,
        TransferBuilder $transferBuilder,
        FrontendInterface $cache,
        SerializerInterface $serializer
    ) {
        $this->config = $config;
        $this->client = $client;
        $this->transferBuilder = $transferBuilder;
        $this->cache = $cache;
        $this->serializer = $serializer;
    }

    /**
     * Get Authentication.
     *
     * @param int|null $storeId
     * @return array
     * @throws ClientException
     * @throws ConverterException
     */
    public function execute(int $storeId = null): array
    {
        $data = $this->getFromCache();

        if (! $data) {
            $data = $this->client->placeRequest(
                $this->createTransfer($storeId)
            );

            $this->saveToCache($data);
        }

        return $data;
    }

    /**
     * Create Transfer object to use in Zend client.
     *
     * @param int|null $storeId
     * @return TransferInterface
     */
    private function createTransfer(int $storeId = null): TransferInterface
    {
        $uri = $this->config->isSandbox($storeId)
            ? $this->config->getSandboxUrl($storeId)
            : $this->config->getProductionUrl($storeId);
        $uri .= self::PATH;

        return $this->transferBuilder
            ->setUri($uri)
            ->setBody($this->createBody($storeId))
            ->setMethod(self::METHOD)
            ->shouldEncode(true)
            ->build();
    }

    /**
     * Generate authentication request body.
     *
     * @param int|null $storeId
     * @return array
     */
    private function createBody(int $storeId = null): array
    {
        return [
            'client_id' => $this->config->getClientId($storeId),
            'client_secret' => $this->config->getClientSecret($storeId),
            'username' => $this->config->getUsername($storeId),
            'password' => $this->config->getPassword($storeId),
            'grant_type' => self::GRANT_TYPE,
        ];
    }

    private function getFromCache(): ?array
    {
        $data = $this->cache->load(self::CACHE_IDENTIFIER);

        if ($data && is_string($data)) {
            return $this->serializer->unserialize($data);
        }

        return null;
    }

    private function saveToCache(array $data): void
    {
        $this->cache->save(
            $this->serializer->serialize($data),
            self::CACHE_IDENTIFIER,
            self::CACHE_TAGS,
            self::CACHE_LIFETIME
        );
    }
}
