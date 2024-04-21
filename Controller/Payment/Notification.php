<?php

declare(strict_types=1);

namespace Volt\Payment\Controller\Payment;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Throwable;
use Volt\Payment\Exception\NotificationException;
use Volt\Payment\Gateway\NotificationProcessor;

class Notification implements HttpPostActionInterface, CsrfAwareActionInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

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
     * @param RequestInterface $request
     * @param RawFactory $resultRawFactory
     * @param Json $json
     * @param LoggerInterface $logger
     * @param NotificationProcessor $notificationProcessor
     */
    public function __construct(
        RequestInterface      $request,
        RawFactory            $resultRawFactory,
        Json                  $json,
        LoggerInterface       $logger,
        NotificationProcessor $notificationProcessor
    ) {
        $this->request = $request;
        $this->resultRawFactory = $resultRawFactory;
        $this->json = $json;
        $this->logger = $logger;
        $this->notificationProcessor = $notificationProcessor;
    }

    public function execute()
    {
        $response = $this->resultRawFactory->create();

        try {
            $content = $this->request->getContent();

            $subject = [
                'body' => $content,
                'params' => $this->json->unserialize($content),
                'headers' => $this->request->getHeaders()->toArray(),
            ];

            $this->notificationProcessor->execute($subject);

            $response->setStatusHeader(200);
            $response->setContents('OK');

            $this->logger->debug('Notification processed', [
                'request' => [
                    'uri' => $this->request->getRequestUri(),
                    'headers' => $this->request->getHeaders()->toArray(),
                    'content' => $this->request->getContent(),
                ],
            ]);
        } catch(NotificationException $e) {
            $response->setStatusHeader(400);
            $response->setContents($e->getMessage());

            $this->logger->error($e->getMessage(), [
                'request' => [
                    'uri' => $this->request->getRequestUri(),
                    'headers' => $this->request->getHeaders()->toArray(),
                    'content' => $this->request->getContent(),
                ],
            ]);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), [
                'request' => [
                    'uri' => $this->request->getRequestUri(),
                    'headers' => $this->request->getHeaders()->toArray(),
                    'content' => $this->request->getContent(),
                ],
            ]);

            $response->setStatusHeader(400);
            $response->setContents($e->getMessage());
        }

        return $response;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param RequestInterface $request
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
