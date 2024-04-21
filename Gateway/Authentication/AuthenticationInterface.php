<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Authentication;

interface AuthenticationInterface
{
    /**
     * Generate access token.
     *
     * @param int|null $storeId
     * @return array
     */
    public function execute(int $storeId = null): array;
}
