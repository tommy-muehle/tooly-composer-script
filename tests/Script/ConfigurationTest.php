<?php

namespace Tooly\Tests\Script;

use Composer\Composer;
use Composer\Config;
use Composer\Package\Package;
use Tooly\Script\Configuration;

/**
 * @package Tooly\Tests\Script
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testIfNoToolsSetEmptyToolSetIsGiven()
    {
        $configuration = new Configuration($this->getPreparedComposerInstance([], ''));
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

        $configuration = new Configuration($this->getPreparedComposerInstance($extra, ''));
        $this->assertCount(1, $configuration->getTools());
    }

    public function testCanCheckDevMode()
    {
        $configuration = new Configuration($this->getPreparedComposerInstance([], ''));
        $this->assertTrue($configuration->isDevMode());
    }

    public function testCanSetDevMode()
    {
        $configuration = new Configuration($this->getPreparedComposerInstance([], ''), false);
        $this->assertFalse($configuration->isDevMode());
    }

    public function testCanCheckInteractiveMode()
    {
        $configuration = new Configuration($this->getPreparedComposerInstance([], ''));
        $this->assertTrue($configuration->isInteractiveMode());
    }

    public function testCanSetInteractiveMode()
    {
        $configuration = new Configuration($this->getPreparedComposerInstance([], ''), true, false);
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
