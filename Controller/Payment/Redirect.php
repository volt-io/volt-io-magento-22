<?php

declare(strict_types=1);

namespace Volt\Payment\Controller\Payment;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\RedirectFactory;
use Volt\Payment\Gateway\Response\RedirectUrlHandler;

class Redirect extends Action
{
    /**
     * @var RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * Redirect constructor.
     *
     * @param  Context  $context
     * @param  RedirectFactory  $redirectFactory
     * @param  Session  $checkoutSession
     */
    public function __construct(
        Context $context,
        RedirectFactory $redirectFactory,
        Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->redirectFactory = $redirectFactory;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute()
    {
        $result = $this->redirectFactory->create();

        $order = $this->checkoutSession->getLastRealOrder();
        $payment = $order->getPayment();

        $result->setUrl(
            $payment->getAdditionalInformation(RedirectUrlHandler::REDIRECT_URL)
        );

        return $result;
    }
}
