<?php

namespace Tooly\Tests\Script\Decision;

use Tooly\Factory\ToolFactory;
use Tooly\Model\Tool;
use Tooly\Script\Decision\FileAlreadyExistDecision;

/**
 * @package Tooly\Tests\Script\Decision
 */
class FileAlreadyExistDecisionTest extends DecisionTestCase
{
    public function testIfFileNotAlreadyExistReturnsTrue()
    {
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

    public function testIfFileAlreadyExistReturnsFalse()
    {
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
