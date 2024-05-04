<?php

declare(strict_types=1);

namespace Volt\Payment\Model;

class StatusEnum
{
    // Pending statuses
    const PENDING = 'PENDING';
    const DELAYED_AT_BANK = 'DELAYED_AT_BANK';
    const COMPLETED = 'COMPLETED';

    // Failed statuses
    const FAILED = 'FAILED';
    const REFUSED_BY_BANK = 'REFUSED_BY_BANK';
    const ERROR_AT_BANK = 'ERROR_AT_BANK';
    const CANCELLED_BY_USER = 'CANCELLED_BY_USER';
    const ABANDONED = 'ABANDONED';

    // Success statuses
    const RECEIVED = 'RECEIVED';
}
