<?php

namespace Tooly\Tests\Script\Decision;

use Tooly\Script\Configuration;
use Tooly\Script\Helper;

/**
 * @package Tooly\Tests\Script\Decision
 */
abstract class DecisionTestCase extends \PHPUnit_Framework_TestCase
{
    protected $helper;

    protected $configuration;

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
}
