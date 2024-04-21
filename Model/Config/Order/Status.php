<?php

declare(strict_types=1);

namespace Volt\Payment\Model\Config\Order;

/**
 * Order Statuses source model
 */
class Status extends \Magento\Sales\Model\Config\Source\Order\Status
{
    /**
     * @var string|bool
     */
    protected $_stateStatuses = false;
}
