<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Validator\Notification;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Psr\Log\LoggerInterface;
use Volt\Payment\Gateway\Config\Config;

class HeaderValidator extends AbstractValidator
{
    /**
     * Delimiter for verifying signature
     */
    protected const DELIMITER = '|';

    /**
     * Algorithm for verifying signature
     */
    protected const ALGORITHM = 'sha256';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        ResultInterfaceFactory $resultFactory,
        Config $config,
        LoggerInterface $logger
    ) {
        parent::__construct($resultFactory);
        $this->config = $config;
        $this->logger = $logger;
    }

    public function validate(array $validationSubject): ResultInterface
    {
        $version = $this->extractVersion(
            $validationSubject['headers']['User-Agent'] ?? false
        );
        $timed = $validationSubject['headers']['X-Volt-Timed'] ?? false;
        $signed = $validationSubject['headers']['X-Volt-Signed'] ?? false;

        if (!$version || !$timed || !$signed) {
            return $this->createResult(false, [
                'Missing or invalid headers'
            ]);
        }

        $body = $validationSubject['body'] ?? '{}';

        $data = implode(self::DELIMITER,
            [
                $body,
                $timed,
                $version,
            ]
        );

        $hash = hash_hmac(
            self::ALGORITHM,
            $data,
            $this->config->getNotificationSecret()
        );

        $this->logger->debug('HeaderValidator::validate', [
            'data' => $data,
            'algorithm' => self::ALGORITHM,
            'hash' => $hash,
            'signed' => $signed,
        ]);

        if ($signed !== $hash) {
            return $this->createResult(false, [
                'Invalid signature'
            ]);
        }

        return $this->createResult(true);
    }

    protected function extractVersion(string $userAgent): string
    {
        $matches = [];
        preg_match('/Volt\/([0-9\.]+)/', $userAgent, $matches);
        return $matches[1] ?? '';
    }
}
