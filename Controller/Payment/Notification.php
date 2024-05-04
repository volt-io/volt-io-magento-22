<?php

declare(strict_types=1);

namespace Volt\Payment\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Throwable;
use Volt\Payment\Exception\NotificationException;
use Volt\Payment\Gateway\NotificationProcessor;

class Notification extends Action
{
    /**
     * @var RawFactory
     */
    private $resultRawFactory;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var NotificationProcessor
     */
    private $notificationProcessor;

    /**
     * Notification constructor.
     *
     * @param  Context  $context
     * @param  RawFactory  $resultRawFactory
     * @param  Json  $json
     * @param  LoggerInterface  $logger
     * @param  NotificationProcessor  $notificationProcessor
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        Json $json,
        LoggerInterface $logger,
        NotificationProcessor $notificationProcessor
    ) {
        parent::__construct($context);

        $this->resultRawFactory = $resultRawFactory;
        $this->json = $json;
        $this->logger = $logger;
        $this->notificationProcessor = $notificationProcessor;
    }

    public function execute()
    {
        $response = $this->resultRawFactory->create();

        try {
            $content = $this->_request->getContent();

            $subject = [
                'body' => $content,
                'params' => $this->json->unserialize($content),
                'headers' => $this->_request->getHeaders()->toArray(),
            ];

            $this->notificationProcessor->execute($subject);

            $response->setStatusHeader(200);
            $response->setContents('OK');

            $this->logger->debug('Notification processed', [
                'request' => [
                    'uri' => $this->_request->getRequestUri(),
                    'headers' => $this->_request->getHeaders()->toArray(),
                    'content' => $this->_request->getContent(),
                ],
            ]);
        } catch(NotificationException $e) {
            $response->setStatusHeader(400);
            $response->setContents($e->getMessage());

            $this->logger->error($e->getMessage(), [
                'request' => [
                    'uri' => $this->_request->getRequestUri(),
                    'headers' => $this->_request->getHeaders()->toArray(),
                    'content' => $this->_request->getContent(),
                ],
            ]);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), [
                'request' => [
                    'uri' => $this->_request->getRequestUri(),
                    'headers' => $this->_request->getHeaders()->toArray(),
                    'content' => $this->_request->getContent(),
                ],
            ]);

            $response->setStatusHeader(400);
            $response->setContents($e->getMessage());
        }

        return $response;
    }
}
