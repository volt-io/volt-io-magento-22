<?php

declare(strict_types=1);

namespace Volt\Payment\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Volt\Payment\Gateway\SubjectReader;

class StoreIdDataBuilder implements BuilderInterface
{
    const STORE_ID = 'store_id';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    public function __construct(
        SubjectReader $subjectReader
    ) {
        $this->subjectReader = $subjectReader;
    }

    public function build(array $buildSubject): array
    {
        return [
            self::STORE_ID => $this->subjectReader->readOrderStoreId($buildSubject),
        ];
    }
}
