<?php

namespace Tooly\Tests\Script\Decision;

use Tooly\Factory\ToolFactory;
use Tooly\Model\Tool;
use Tooly\Script\Decision\IsAccessibleDecision;
use Tooly\Script\Helper\Downloader;

/**
 * @package Tooly\Tests\Script\Decision
 */
class IsAccessibleDecisionTest extends DecisionTestCase
{
    public function testNotAccessibleToolUrlReturnsFalse()
    {
        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->getMock();

        $downloader
            ->expects($this->once())
            ->method('isAccessible')
            ->willReturn(false);

        $this->helper
            ->expects($this->once())
            ->method('getDownloader')
            ->willReturn($downloader);

        $decision = new IsAccessibleDecision($this->configuration, $this->helper);
        $this->assertFalse($decision->canProceed(ToolFactory::createTool('tool', __DIR__, [])));
    }

    public function testEmptySignUrlReturnsTrue()
    {
        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->getMock();

        $downloader
            ->expects($this->once())
            ->method('isAccessible')
            ->willReturn(true);

        $this->helper
            ->expects($this->once())
            ->method('getDownloader')
            ->willReturn($downloader);

        $decision = new IsAccessibleDecision($this->configuration, $this->helper);
        $this->assertTrue($decision->canProceed(ToolFactory::createTool('tool', __DIR__, [])));
    }

    public function testNotAccessibleToolSignUrlReturnsFalse()
    {
        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->getMock();

        $downloader
            ->expects($this->exactly(2))
            ->method('isAccessible')
            ->will($this->onConsecutiveCalls(true, false));

        $this->helper
            ->expects($this->exactly(2))
            ->method('getDownloader')
            ->willReturn($downloader);

        $decision = new IsAccessibleDecision($this->configuration, $this->helper);
        $this->assertFalse($decision->canProceed(ToolFactory::createTool('tool', __DIR__, [
            'sign-url' => 'sign-url'
        ])));
    }

    public function testNotAccessibleToolUrlButAccessibleFallbackUrlReturnsTrue()
    {
        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->getMock();

        $downloader
            ->expects($this->exactly(2))
            ->method('isAccessible')
            ->will($this->onConsecutiveCalls(false, true));

        $this->helper
            ->expects($this->exactly(2))
            ->method('getDownloader')
            ->willReturn($downloader);

        $decision = new IsAccessibleDecision($this->configuration, $this->helper);
        $this->assertTrue($decision->canProceed(ToolFactory::createTool('tool', __DIR__, [
            'fallback-url' => 'fallback-url'
        ])));
    }

    public function testAccessibleUrlsWillReturnTrue()
    {
        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->getMock();

        $downloader
            ->expects($this->exactly(2))
            ->method('isAccessible')
            ->will($this->onConsecutiveCalls(true, true));

        $this->helper
            ->expects($this->exactly(2))
            ->method('getDownloader')
            ->willReturn($downloader);

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
