<?php

declare(strict_types=1);

namespace Volt\Payment\Model\Logger;

use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Handler extends Base
{
    const LOG_FILE_NAME_PREFIX = 'Volt';
    const LOG_MAIN_DIR         = '/var/log/volt/';
    const LOG_FILE_DATE_FORMAT = 'Y-m-d';

    /**
     * Logging level
     *
     * @var int
     */
    public $loggerType = Logger::DEBUG;

    /**
     * @var string
     */
    public $fileName = '';

    /**
     * Handler constructor.
     *
     * @param DriverInterface $filesystem
     */
    public function __construct(DriverInterface $filesystem)
    {
        $this->setLogFileName();

        parent::__construct($filesystem);
    }

    /**
     * @return void
     */
    public function setLogFileName()
    {
        $this->fileName = self::LOG_MAIN_DIR
            . '/' . self::LOG_FILE_NAME_PREFIX
            . '_' . $this->getFileSuffixAsDate() . '.log';
    }

    /**
     * @return false|string
     */
    public function getFileSuffixAsDate()
    {
        return date(self::LOG_FILE_DATE_FORMAT);
    }
}
