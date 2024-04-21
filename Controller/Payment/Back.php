<?php

declare(strict_types=1);

namespace Volt\Payment\Controller\Payment;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Volt\Payment\Model\StatusEnum;

class Back implements HttpGetActionInterface
{
    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * Back constructor.
     *
     * @param ResultFactory $resultFactory
     * @param RequestInterface $request
     * @param Json $json
     * @param LoggerInterface $logger
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        ResultFactory $resultFactory,
        RequestInterface $request,
        Json $json,
        LoggerInterface $logger,
        MessageManagerInterface $messageManager
    ) {
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->json = $json;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        $data = $this->request->getParam('volt');

        if ($data && is_string($data)) {
            $data = $this->json->unserialize(base64_decode($data));
        }

        if (! isset($data['status'])) {
            $this->logger->error('Invalid back data', [
                'params' => $this->request->getParams(),
                'data' => $data
            ]);
            $this->messageManager->addErrorMessage(__('Invalid return data'));

            return $this->prepareResponseForFailure();
        }

        switch ($data['status']) {
            case StatusEnum::PENDING:
            case StatusEnum::DELAYED_AT_BANK:
                return $this->prepareResponseForPending($data);
            case StatusEnum::COMPLETED:
                return $this->prepareResponseForCompleted();
            default:
                return $this->prepareResponseForFailure();
        }
    }

    /**
     * Prepare response for pending status (waiting for payment confirmation).
     *
     * @param $data
     * @return Redirect
     */
    private function prepareResponseForPending($data): Redirect
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $result->getConfig()->getTitle()->set(__('Waiting for payment confirmation'));

        return $result;
    }

    /**
     * Prepare response for success status.
     *
     * @return Redirect
     */
    private function prepareResponseForCompleted(): Redirect
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $result->setPath('checkout/onepage/success');

        return $result;
    }

    /**
     * Prepare response for failure status.
     *
     * @return Redirect
     */
    private function prepareResponseForFailure(): Redirect
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $result->setPath('checkout/onepage/failure');

        return $result;
    }
}
