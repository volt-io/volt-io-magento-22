<?php

declare(strict_types=1);

namespace Volt\Payment\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\Page;
use Psr\Log\LoggerInterface;
use Volt\Payment\Model\StatusEnum;

class Back extends Action
{
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
     * @param  Context  $context
     * @param  ResultFactory  $resultFactory
     * @param  Json  $json
     * @param  LoggerInterface  $logger
     * @param  MessageManagerInterface  $messageManager
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        Json $json,
        LoggerInterface $logger,
        MessageManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultFactory;
        $this->json = $json;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        $data = $this->_request->getParam('volt');

        if ($data && is_string($data)) {
            $data = $this->json->unserialize(base64_decode($data));
        }

        if (! isset($data['status'])) {
            $this->logger->error('Invalid back data', [
                'params' => $this->_request->getParams(),
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
     * @return Page
     */
    private function prepareResponseForPending($data): Page
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
