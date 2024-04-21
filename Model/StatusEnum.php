<?php

declare(strict_types=1);

namespace Volt\Payment\Model;

class StatusEnum
{
    // Pending statuses
    public const PENDING = 'PENDING';
    public const DELAYED_AT_BANK = 'DELAYED_AT_BANK';
    public const COMPLETED = 'COMPLETED';

    // Failed statuses
    public const FAILED = 'FAILED';
    public const REFUSED_BY_BANK = 'REFUSED_BY_BANK';
    public const ERROR_AT_BANK = 'ERROR_AT_BANK';
    public const CANCELLED_BY_USER = 'CANCELLED_BY_USER';
    public const ABANDONED = 'ABANDONED';

    // Success statuses
    public const RECEIVED = 'RECEIVED';
}
