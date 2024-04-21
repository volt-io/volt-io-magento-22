<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Request;

use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Psr\Log\LoggerInterface;
use Volt\Payment\Exception\AuthorizationException;
use Volt\Payment\Gateway\Authentication\Authentication;
use Volt\Payment\Gateway\SubjectReader;

class AuthenticationDataBuilder implements BuilderInterface
{
    public const BEARER_TOKEN = 'bearer_token';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var Authentication
     */
    private $authentication;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        SubjectReader $subjectReader,
        Authentication $authentication,
        LoggerInterface $logger
    ) {
        $this->subjectReader = $subjectReader;
        $this->authentication = $authentication;
        $this->logger = $logger;
    }

    /**
     * @throws ConverterException
     * @throws ClientException
     */
    public function build(array $buildSubject): array
    {
        $storeId = $this->subjectReader->readOrderStoreId($buildSubject);

        return [
            self::BEARER_TOKEN => $this->getBearer($storeId)
        ];
    }

    /**
     * Get Bearer token to use in Zend client.
     *
     * @param int|null $storeId
     * @return string
     * @throws ClientException
     * @throws ConverterException
     */
    private function getBearer(int $storeId = null): string
    {
        $data = $this->authentication->execute($storeId);

        if (!is_array($data)) {
            $this->logger->error('Invalid response from Volt API', $data);
            throw new AuthorizationException(__('Invalid response from Volt API. Please try again later.'));
        }

        if (isset($data['code']) && $data['code'] === 401) {
            $this->logger->error('Error response from Volt API', $data);
            throw new AuthorizationException(__('Invalid response from Volt API. Please try again later.'));
        }

        if (!isset($data['access_token'])) {
            $this->logger->error('Missing access token from Volt API', $data);
            throw new AuthorizationException(__('Invalid response from Volt API. Please try again later.'));
        }

        return $data['access_token'];
    }
}
