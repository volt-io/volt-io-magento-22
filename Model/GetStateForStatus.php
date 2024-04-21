<?php

declare(strict_types=1);

namespace Volt\Payment\Model;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\ResourceModel\Order\Status\Collection;

class GetStateForStatus
{
    /** @var Collection */
    private $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Get order state for order status
     *
     * @param string $status
     * @param string $default
     * @return mixed|string
     */
    public function execute(string $status, string $default = Order::STATE_NEW)
    {
        if (!empty($status)) {
            foreach ($this->collection->joinStates() as $item) {
                /** @var Status $item */
                if ($item->getStatus() == $status) {
                    return $item->getState();
                }
            }
        }

        return $default;
    }
}
