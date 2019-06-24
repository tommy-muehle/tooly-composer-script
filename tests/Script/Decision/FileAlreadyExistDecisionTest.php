<?php

namespace Tooly\Tests\Script\Decision;

use Tooly\Model\Tool;
use Tooly\Script\Decision\FileAlreadyExistDecision;
use Tooly\Script\Helper\Downloader;

/**
 * @package Tooly\Tests\Script\Decision
 */
class FileAlreadyExistDecisionTest extends DecisionTestCase
{
    public function testIfFileIsAccessibleAndFileNotAlreadyExistReturnsTrue()
    {
        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $downloader
            ->expects($this->once())
            ->method('isAccessible')
            ->willReturn(true);

        $this->helper
            ->expects($this->once())
            ->method('getDownloader')
            ->willReturn($downloader);

        $this->helper
            ->expects($this->once())
            ->method('isFileAlreadyExist')
            ->willReturn(false);

        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $decision = new FileAlreadyExistDecision($this->configuration, $this->helper);
        $this->assertTrue($decision->canProceed($tool));
    }

    public function testIfFileNotAccessibleAndFileNotAlreadyExistReturnsTrue()
    {
        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $downloader
            ->expects($this->once())
            ->method('isAccessible')
            ->willReturn(false);

        $this->helper
            ->expects($this->once())
            ->method('getDownloader')
            ->willReturn($downloader);

        $this->helper
            ->expects($this->once())
            ->method('isFileAlreadyExist')
            ->willReturn(false);

        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $decision = new FileAlreadyExistDecision($this->configuration, $this->helper);
        $this->assertTrue($decision->canProceed($tool));
    }

    public function testIfFileIsAccessibleAndFileAlreadyExistReturnsFalse()
    {
        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $downloader
            ->expects($this->once())
            ->method('isAccessible')
            ->willReturn(true);

        $this->helper
            ->expects($this->once())
            ->method('getDownloader')
            ->willReturn($downloader);

        $this->helper
            ->expects($this->once())
            ->method('isFileAlreadyExist')
            ->willReturn(true);

        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $decision = new FileAlreadyExistDecision($this->configuration, $this->helper);
        $this->assertFalse($decision->canProceed($tool));
    }

    public function testIfFileNotAccessibleAndFileAlreadyExistReturnsFalse()
    {
        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $downloader
            ->expects($this->once())
            ->method('isAccessible')
            ->willReturn(false);

        $this->helper
            ->expects($this->once())
            ->method('getDownloader')
            ->willReturn($downloader);

        $this->helper
            ->expects($this->once())
            ->method('isFileAlreadyExist')
            ->willReturn(true);

        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $decision = new FileAlreadyExistDecision($this->configuration, $this->helper);
        $this->assertFalse($decision->canProceed($tool));
    }

    public function testCanGetReason()
    {
        $decision = new FileAlreadyExistDecision($this->configuration, $this->helper);
        $this->assertRegExp('/info/', $decision->getReason());
    }
}
