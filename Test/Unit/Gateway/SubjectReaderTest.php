<?php

declare(strict_types=1);

namespace Volt\Payment\Test\Unit\Gateway;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\MockObject\MockObject;
use Volt\Payment\Gateway\SubjectReader;
use PHPUnit\Framework\TestCase;

class SubjectReaderTest extends TestCase
{
    /**
     * @var PaymentDataObjectInterface&MockObject
     */
    private $payment;

    /**
     * @var array
     */
    private $subject;

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payment = $this->getMockForAbstractClass(PaymentDataObjectInterface::class);
        $this->subject = [
            'payment' => $this->payment,
            'amount' => 10.50,
            'response' => [
                'id' => '93b85f3c-76eb-4316-b1ae-f3370ddc59bc',
                'checkoutUrl' => 'https://checkout.volt.io/{paymentId}?auth=jwtToken',
            ],
        ];

        $this->subjectReader = new SubjectReader();
    }

    public function testReadAmount()
    {
        $this->assertEquals(10.50, $this->subjectReader->readAmount($this->subject));
    }

    public function testReadOrderStoreId()
    {
        $orderMock = $this->getMockForAbstractClass(OrderInterface::class);

        $this->payment
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);
        $orderMock
            ->expects($this->once())
            ->method('getStoreId')
            ->willReturn(2);

        $this->assertEquals(2, $this->subjectReader->readOrderStoreId($this->subject));
    }

    public function testReadPayment()
    {
        $this->assertSame($this->payment, $this->subjectReader->readPayment($this->subject));
    }

    public function testReadResponse()
    {
        $this->assertEquals([
            'id' => '93b85f3c-76eb-4316-b1ae-f3370ddc59bc',
            'checkoutUrl' => 'https://checkout.volt.io/{paymentId}?auth=jwtToken',
        ], $this->subjectReader->readResponse($this->subject));
    }

    public function testReadOrderIncrementId()
    {
        $paymentModelMock = $this->getMockBuilder(Payment::class)->disableOriginalConstructor()->getMock();
        $orderMock = $this->getMockForAbstractClass(OrderInterface::class);

        $this->payment
            ->expects($this->once())
            ->method('getPayment')
            ->willReturn($paymentModelMock);
        $paymentModelMock
            ->expects($this->once())
            ->method('getOrder')
            ->willReturn($orderMock);
        $orderMock
            ->expects($this->once())
            ->method('getIncrementId')
            ->willReturn('TEST00000001');

        $this->assertEquals('TEST00000001', $this->subjectReader->readOrderIncrementId($this->subject));
    }
}
