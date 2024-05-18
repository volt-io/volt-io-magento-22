<?php

declare(strict_types=1);

namespace Volt\Payment\Model;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Volt\Payment\Gateway\Config\Config;

class CancelOrder
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var GetStateForStatus $getStateForStatus
     */
    protected $getStateForStatus;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    public function __construct(
        Config $config,
        GetStateForStatus $getStateForStatus,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->config = $config;
        $this->getStateForStatus = $getStateForStatus;
        $this->orderRepository = $orderRepository;
    }

    public function execute(
        OrderInterface $order,
        OrderPaymentInterface $payment,
        string $comment = ''
    ) {
        $order->cancel();

        $status = $this->config->getStatusFailed((int) $order->getStoreId());
        $state = $this->getStateForStatus->execute($status, Order::STATE_CANCELED);

        $payment
            ->setIsTransactionPending(false)
            ->setIsTransactionClosed(false);

        $order
            ->addStatusToHistory($status, $comment, false)
            ->setState($state)
            ->setIsCustomerNotified(false);

        $this->orderRepository->save($order);
    }
}
