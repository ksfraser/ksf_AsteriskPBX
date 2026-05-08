<?php

declare(strict_types=1);

namespace Ksfraser\Unit;

use PHPUnit\Framework\TestCase;
use Ksfraser\AsteriskPBX\Entity\AsteriskCall;

class AsteriskCallTest extends TestCase
{
    public function testCanCreateCall(): void
    {
        $call = new AsteriskCall();
        $this->assertInstanceOf(AsteriskCall::class, $call);
    }

    public function testCanSetAndGetCallerNumber(): void
    {
        $call = new AsteriskCall();
        $call->setCallerNumber('+15551234567');
        $this->assertEquals('+15551234567', $call->getCallerNumber());
    }

    public function testCanSetAndGetCalledNumber(): void
    {
        $call = new AsteriskCall();
        $call->setCalledNumber('+15559876543');
        $this->assertEquals('+15559876543', $call->getCalledNumber());
    }

    public function testCanSetAndGetDirection(): void
    {
        $call = new AsteriskCall();
        $call->setDirection(AsteriskCall::DIRECTION_INBOUND);
        $this->assertEquals(AsteriskCall::DIRECTION_INBOUND, $call->getDirection());
    }

    public function testCanCheckIsInbound(): void
    {
        $call = new AsteriskCall();
        $call->setDirection(AsteriskCall::DIRECTION_INBOUND);
        $this->assertTrue($call->isInbound());
        
        $call->setDirection(AsteriskCall::DIRECTION_OUTBOUND);
        $this->assertFalse($call->isInbound());
    }

    public function testCanLinkToContact(): void
    {
        $call = new AsteriskCall();
        $call->setLinkedContactId(5);
        $call->setLinkedType(AsteriskCall::LINKED_CONTACT);
        $this->assertEquals(5, $call->getLinkedContactId());
        $this->assertEquals(AsteriskCall::LINKED_CONTACT, $call->getLinkedType());
    }
}