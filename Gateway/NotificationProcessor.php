<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;
use Volt\Payment\Exception\NotificationException;

class NotificationProcessor
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * @var ValidatorInterface|null
     */
    private $validator;

    public function __construct(
        LoggerInterface $logger,
        Json $serializer,
        CommandPoolInterface $commandPool,
        ValidatorInterface $validator = null
    ) {
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->commandPool = $commandPool;
        $this->validator = $validator;
    }

    /**
     * Process notification.
     *
     * @param array $subject
     * @return void
     * @throws NotificationException
     */
    public function execute(array $subject): void
    {
        if ($this->validator) {
            $validationResult = $this->validator->validate($subject);
            if (!$validationResult->isValid()) {
                $this->logger->error(
                    'Notification validation failed: ' . implode(', ', $validationResult->getFailsDescription()),
                    ['subject' => $subject]
                );

                throw new NotificationException(
                    __('Notification validation failed: ' . implode(', ', $validationResult->getFailsDescription()))
                );
            }
        }

        $data = $subject['params'];

        $status = strtolower($data['status'] ?? 'testing-fake-notification');
        if ($status === 'testing-fake-notification') {
            // Only for testing purposes - Volt sending fake notification without any params.
            return;
        }

        try {
            $command = $this->commandPool->get($status);
            $command->execute($data);
        } catch (\Exception $e) {
            $this->logger->error('Process notification failed: ' . $e->getMessage(), [
                'subject' => $subject,
            ]);

            throw new NotificationException(__('Process notification failed: ' . $e->getMessage()));
        }
    }
}
