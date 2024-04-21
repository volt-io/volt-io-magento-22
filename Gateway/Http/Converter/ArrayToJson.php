<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Http\Converter;

use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

class ArrayToJson
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * JsonToArray constructor.
     *
     * @param Json            $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        Json $serializer,
        LoggerInterface $logger
    ) {
        $this->logger     = $logger;
        $this->serializer = $serializer;
    }

    /**
     * Converts gateway response to array structure
     *
     * @param array $response
     * @return string
     * @throws ConverterException
     */
    public function convert(array $response): string
    {
        try {
            return $this->serializer->serialize($response);
        } catch (\Exception $e) {
            $this->logger->critical('Invalid request Volt API. Please try again later.', [
                'response' => $response
            ]);
            throw new ConverterException(__('Invalid request Volt API. Please try again later.'));
        }
    }
}
