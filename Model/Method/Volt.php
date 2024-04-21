<?php

declare(strict_types=1);

namespace Volt\Payment\Model\Method;

use Magento\Payment\Model\Method\Adapter;

class Volt extends Adapter
{
    public function canRefund(): bool
    {
        return $this->getConfigData('refund_enabled') &&
            parent::canRefund();
    }
}
