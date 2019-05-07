<?php

namespace Tooly\Tests\Script\Decision;

use Tooly\Factory\ToolFactory;
use Tooly\Script\Decision\UseFallbackURLDecision;
use Tooly\Script\Helper\Downloader;

/**
 * @package Tooly\Tests\Script\Decision
 */
class UseFallbackUrlDecisionTest extends DecisionTestCase
{
    public function testAccessibleUrlsWillReturnTrue()
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

        $decision = new UseFallbackURLDecision($this->configuration, $this->helper);
        $this->assertTrue($decision->canProceed(ToolFactory::createTool('tool', __DIR__, [])));
    }

    public function testEmptySignUrlReturnsTrue()
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

        $decision = new UseFallbackURLDecision($this->configuration, $this->helper);
        $this->assertTrue($decision->canProceed(ToolFactory::createTool('tool', __DIR__, [])));
    }

    public function testNotAccessibleFallbackUrlReturnsFalse()
    {
        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->getMock();

        $downloader
            ->expects($this->exactly(2))
            ->method('isAccessible')
            ->will($this->onConsecutiveCalls(false, false));

        $this->helper
            ->expects($this->exactly(2))
            ->method('getDownloader')
            ->willReturn($downloader);

        $decision = new UseFallbackURLDecision($this->configuration, $this->helper);
        $this->assertFalse($decision->canProceed(ToolFactory::createTool('tool', __DIR__, [
            'fallback-url' => 'fallback-url'
        ])));
    }

   public function testAccessibleFallbackUrlWillReturnTrue()
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

        $decision = new UseFallbackURLDecision($this->configuration, $this->helper);
        $this->assertTrue($decision->canProceed(ToolFactory::createTool('tool', __DIR__, [
            'fallback-url' => 'fallback-url'
        ])));
    }

    public function testCanGetReason()
    {
        $decision = new UseFallbackURLDecision($this->configuration, $this->helper);
        $this->assertRegExp('/error/', $decision->getReason());
    }
}
