<?php

declare(strict_types=1);

namespace Volt\Payment\Console\Command;

use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Volt\Payment\Model\UpdateAbandoned;

class UpdateAbandonedCommand extends Command
{
    /** @var UpdateAbandoned */
    protected $updateAbandonedService;

    public function __construct(UpdateAbandoned $updateAbandonedService)
    {
        parent::__construct();

        $this->updateAbandonedService = $updateAbandonedService;
    }

    protected function configure(): void
    {
        $this->setName('volt:update-abandoned');
        $this->setDescription('Update abandoned payments. After receiving ABANDONED_BY_USER status, we\'re waiting 3 hours to change status of order to failed payment.');

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;

        try {
            $results = $this->updateAbandonedService->execute();

            if ($results) {
                $output->writeln("<info>Updated {$results} orders.</info>");
            } else {
                $output->writeln('<info>No orders to update.</info>');
            }
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            $exitCode = 1;
        }

        return $exitCode;
    }
}
