<?php

declare(strict_types=1);

namespace Volt\Payment\Cron;

use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Volt\Payment\Model\UpdateAbandoned as UpdateAbandonedService;

/**
 * Gateway synchronization CRON Job
 */
class UpdateAbandonedCron
{
    /**
     * @var UpdateAbandonedService
     */
    public $updateAbandonedService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Synchronization constructor.
     */
    public function __construct(
        UpdateAbandonedService $updateAbandonedService,
        LoggerInterface $logger
    ) {
        $this->updateAbandonedService = $updateAbandonedService;
        $this->logger = $logger;
    }

    /**
     * @return $this
     * @throws NoSuchEntityException
     */
    public function execute(): UpdateAbandonedCron
    {
        $int = $this->updateAbandonedService->execute();
        $this->logger->info('Updated ' . $int . ' orders with status ABANDONED_BY_USER');

        return $this;
    }
}
