<?php

namespace Tooly\Tests\Script\Decision;

use Tooly\Model\Tool;
use Tooly\Script\Configuration;
use Tooly\Script\Decision\OnlyDevDecision;
use Tooly\Script\Helper;

/**
 * @package Tooly\Tests\Script\Decision
 */
class OnlyDevDecisionTest extends \PHPUnit_Framework_TestCase
{
    private $helper;

    private $configuration;

    public function setUp()
    {
        $this->helper = $this
            ->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configuration = $this
            ->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testOnlyDevToolInNonDevModeReturnsFalse()
    {
        $helper = clone $this->helper;
        $configuration = clone $this->configuration;

        $configuration
            ->expects($this->once())
            ->method('isDevMode')
            ->willReturn(false);

        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tool
            ->expects($this->once())
            ->method('isOnlyDev')
            ->willReturn(true);

        $decision = new OnlyDevDecision($configuration, $helper);
        $this->assertFalse($decision->canProceed($tool));
    }

    public function testNonOnlyDevToolInNonDevModeReturnsTrue()
    {
        $helper = clone $this->helper;
        $configuration = clone $this->configuration;

        $configuration
            ->expects($this->once())
            ->method('isDevMode')
            ->willReturn(false);

        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tool
            ->expects($this->once())
            ->method('isOnlyDev')
            ->willReturn(false);

        $decision = new OnlyDevDecision($configuration, $helper);
        $this->assertTrue($decision->canProceed($tool));
    }

    public function testNonOnlyDevToolInDevModeReturnsTrue()
    {
        $helper = clone $this->helper;
        $configuration = clone $this->configuration;

        $configuration
            ->expects($this->once())
            ->method('isDevMode')
            ->willReturn(true);

        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tool
            ->expects($this->never())
            ->method('isOnlyDev');

        $decision = new OnlyDevDecision($configuration, $helper);
        $this->assertTrue($decision->canProceed($tool));
    }
}
