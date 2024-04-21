<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Command;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Volt\Payment\Gateway\Config\Config;
use Volt\Payment\Model\GetStateForStatus;
use Volt\Payment\Model\Repository\TransactionRepository;

abstract class AbstractPaymentCommand implements CommandInterface
{
    public const KEY_PAYMENT = 'payment';
    public const KEY_REFERENCE = 'reference';
    public const KEY_AMOUNT = 'amount';
    public const KEY_STATUS = 'status';

    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var OrderPaymentRepositoryInterface
     */
    protected $orderPaymentRepository;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var GetStateForStatus $getStateForStatus
     */
    protected $getStateForStatus;

    public function __construct(
        TransactionRepository $transactionRepository,
        OrderPaymentRepositoryInterface $orderPaymentRepository,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        Config $config,
        GetStateForStatus $getStateForStatus
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->orderPaymentRepository = $orderPaymentRepository;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        $this->config = $config;
        $this->getStateForStatus = $getStateForStatus;
    }

    /**
     * Process payment.
     *
     * @param TransactionInterface $transaction
     * @param OrderPaymentInterface $payment
     * @param array $commandSubject
     * @return void
     */
    abstract public function process(
        TransactionInterface $transaction,
        OrderPaymentInterface $payment,
        array $commandSubject
    ): void;

    /**
     * Execute command basing on business object
     *
     * @param array $commandSubject
     * @return void
     */
    public function execute(array $commandSubject): void
    {
        try {
            $transaction = $this->getTransaction($commandSubject);
        } catch (NoSuchEntityException $e) {
            $this->logger->error('Transaction not found', [
                'commandSubject' => $commandSubject,
            ]);
            return;
        }

        try {
            $payment = $this->getPaymentByTransaction($transaction);
        } catch (NoSuchEntityException $e) {
            $this->logger->error('Payment not found', [
                'commandSubject' => $commandSubject,
            ]);
            return;
        }

        $this->process($transaction, $payment, $commandSubject);
    }

    /**
     * Get Magento transaction by txn id.
     *
     * @param array $commandSubject
     * @return TransactionInterface|null
     * @throws NoSuchEntityException
     */
    protected function getTransaction(array $commandSubject): ?TransactionInterface
    {
        $txnId = $commandSubject[self::KEY_PAYMENT];

        return $this->transactionRepository->getByTxnId($txnId);
    }

    /**
     * @param TransactionInterface $transaction
     *
     * @return OrderPaymentInterface
     */
    protected function getPaymentByTransaction(TransactionInterface $transaction): ?OrderPaymentInterface
    {
        return $this->orderPaymentRepository->get($transaction->getPaymentId());
    }

    /**
     * Generate comment for status history.
     *
     * @param array $commandSubject
     * @return string
     */
    protected function getComment(array $commandSubject): string
    {
        $comment = '[Volt: Pay by Bank] Received notification';
        $comment .= ' - Reference: ' . $commandSubject[self::KEY_REFERENCE];
        $comment .= ' - Amount: ' . $commandSubject[self::KEY_AMOUNT];
        $comment .= ' - Status: ' . $commandSubject[self::KEY_STATUS];

        return $comment;
    }
}
