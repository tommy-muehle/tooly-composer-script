<?php

namespace Tooly\Tests\Script\Decision;

use Tooly\Script\Configuration;
use Tooly\Script\Helper;
use PHPUnit\Framework\TestCase;

/**
 * @package Tooly\Tests\Script\Decision
 */
abstract class DecisionTestCase extends TestCase
{
    protected $helper;

    protected $configuration;

    protected function setUp()
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
}
