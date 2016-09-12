<?php

namespace Tooly\Tests\Script;

use Composer\Composer;
use Composer\Config;
use Composer\Package\Package;
use Tooly\Script\Configuration;
use Tooly\Script\Mode;

/**
 * @package Tooly\Tests\Script
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testIfNoToolsSetEmptyToolSetIsGiven()
    {
        $configuration = new Configuration($this->getPreparedComposerInstance([], ''), new Mode(true, false));
        $this->assertCount(0, $configuration->getTools());
    }

    public function testCanGetCorrectToolSet()
    {
        $extra = [
            'tools' => [
                'foo' => [
                    'url' => 'foo'
                ]
            ]
        ];

        $configuration = new Configuration($this->getPreparedComposerInstance($extra, ''), new Mode);
        $this->assertCount(1, $configuration->getTools());
    }

    public function testCanCheckDevMode()
    {
        $configuration = new Configuration($this->getPreparedComposerInstance([], ''), new Mode);
        $this->assertTrue($configuration->isDevMode());
    }

    public function testCanSetDevMode()
    {
        $mode = new Mode;
        $mode->setNoDev();

        $configuration = new Configuration($this->getPreparedComposerInstance([], ''), $mode);
        $this->assertFalse($configuration->isDevMode());
    }

    public function testCanCheckInteractiveMode()
    {
        $configuration = new Configuration($this->getPreparedComposerInstance([], ''), new Mode);
        $this->assertTrue($configuration->isInteractiveMode());
    }

    public function testCanSetInteractiveMode()
    {
        $mode = new Mode;
        $mode->setNonInteractive();

        $configuration = new Configuration($this->getPreparedComposerInstance([], ''), $mode);
        $this->assertFalse($configuration->isInteractiveMode());
    }

    /**
     * @param mixed $extra
     * @param mixed $binDir
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getPreparedComposerInstance($extra, $binDir)
    {
        $package = $this
            ->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->getMock();

        $package
            ->expects($this->once())
            ->method('getExtra')
            ->willReturn($extra);

        $configuration = $this
            ->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configuration
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('bin-dir'))
            ->willReturn($binDir);

        $composer = $this
            ->getMockBuilder(Composer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $composer
            ->expects($this->once())
            ->method('getPackage')
            ->willReturn($package);

        $composer
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn($configuration);

        return $composer;
    }
}
