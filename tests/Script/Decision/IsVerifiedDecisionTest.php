<?php

namespace Tooly\Tests\Script\Decision;

use Tooly\Factory\ToolFactory;
use Tooly\Model\Tool;
use Tooly\Script\Decision\IsVerifiedDecision;

/**
 * @package Tooly\Tests\Script\Decision
 */
class IsVerifiedDecisionTest extends DecisionTestCase
{
    public function testEmptySignUrlReturnsTrue()
    {
        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tool
            ->expects($this->once())
            ->method('getSignUrl')
            ->willReturn(null);

        $decision = new IsVerifiedDecision($this->configuration, $this->helper);
        $this->assertTrue($decision->canProceed($tool));
    }

    public function testVerificationReturnsBool()
    {
        $helper = $this->helper;
        $helper
            ->expects($this->at(0))
            ->method('isVerified')
            ->willReturn(true);

        $helper
            ->expects($this->at(1))
            ->method('isVerified')
            ->willReturn(false);

        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tool
            ->expects($this->exactly(2))
            ->method('getUrl')
            ->willReturn('foo');

        $tool
            ->expects($this->exactly(4))
            ->method('getSignUrl')
            ->willReturn('bar');

        $decision = new IsVerifiedDecision($this->configuration, $helper);

        $this->assertTrue($decision->canProceed($tool));
        $this->assertFalse($decision->canProceed($tool));
    }

    public function testCanGetReason()
    {
        $decision = new IsVerifiedDecision($this->configuration, $this->helper);
        $this->assertRegExp('/error/', $decision->getReason());
    }
}
