<?php

namespace Tooly\Tests\Script\Decision;

use Tooly\Factory\ToolFactory;
use Tooly\Model\Tool;
use Tooly\Script\Decision\IsAccessibleDecision;

/**
 * @package Tooly\Tests\Script\Decision
 */
class IsAccessibleDecisionTest extends DecisionTestCase
{
    public function testNotAccessibleToolUrlReturnsFalse()
    {
        $this->helper
            ->expects($this->once())
            ->method('isAccessible')
            ->willReturn(false);

        $decision = new IsAccessibleDecision($this->configuration, $this->helper);
        $this->assertFalse($decision->canProceed(ToolFactory::createTool('tool', __DIR__, [])));
    }

    public function testEmptySignUrlReturnsTrue()
    {
        $this->helper
            ->expects($this->once())
            ->method('isAccessible')
            ->willReturn(true);

        $decision = new IsAccessibleDecision($this->configuration, $this->helper);
        $this->assertTrue($decision->canProceed(ToolFactory::createTool('tool', __DIR__, [])));
    }

    public function testNotAccessibleToolSignUrlReturnsFalse()
    {
        $this->helper
            ->method('isAccessible')
            ->will($this->onConsecutiveCalls(true, false));

        $decision = new IsAccessibleDecision($this->configuration, $this->helper);
        $this->assertFalse($decision->canProceed(ToolFactory::createTool('tool', __DIR__, [
            'sign-url' => 'sign-url'
        ])));
    }

    public function testAccessibleUrlsWillReturnTrue()
    {
        $this->helper
            ->method('isAccessible')
            ->will($this->onConsecutiveCalls(true, true));

        $decision = new IsAccessibleDecision($this->configuration, $this->helper);
        $this->assertTrue($decision->canProceed(ToolFactory::createTool('tool', __DIR__, [
            'sign-url' => 'sign-url'
        ])));
    }

    public function testCanGetReason()
    {
        $decision = new IsAccessibleDecision($this->configuration, $this->helper);
        $this->assertRegExp('/error/', $decision->getReason());
    }
}
