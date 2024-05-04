<?php

declare(strict_types=1);

namespace Volt\Payment\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;

class Verify extends Action
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Verify constructor.
     *
     * @param  Context  $context
     * @param  RequestInterface  $request
     * @param  LoggerInterface  $logger
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->request = $request;
        $this->logger = $logger;
    }

    public function execute()
    {
        // TODO: Implement execute() method.
    }
}
