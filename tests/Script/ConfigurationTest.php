<?php

namespace Tooly\Tests\Script;

use Composer\Composer;
use Composer\Config;
use Composer\Package\Package;
use Tooly\Script\Configuration;
use Tooly\Script\Mode;
use PHPUnit\Framework\TestCase;

/**
 * @package Tooly\Tests\Script
 */
class ConfigurationTest extends TestCase
{
    public function testIfNoToolsSetEmptyToolSetIsGiven()
    {
        $composer = $this->getPreparedComposerInstance([], '');
        $configuration = new Configuration($composer, new Mode);

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

        $composer = $this->getPreparedComposerInstance($extra, '');
        $configuration = new Configuration($composer, new Mode);

        $this->assertCount(1, $configuration->getTools());
    }

    public function testCanCheckDevMode()
    {
        $composer = $this->getPreparedComposerInstance([], '');
        $configuration = new Configuration($composer, new Mode);

        $this->assertTrue($configuration->isDevMode());
    }

    public function testCanSetDevMode()
    {
        $mode = new Mode;
        $mode->setNoDev();

        $composer = $this->getPreparedComposerInstance([], '');
        $configuration = new Configuration($composer, $mode);

        $this->assertFalse($configuration->isDevMode());
    }

    public function testCanCheckInteractiveMode()
    {
        $composer = $this->getPreparedComposerInstance([], '');
        $configuration = new Configuration($composer, new Mode);

        $this->assertTrue($configuration->isInteractiveMode());
    }

    public function testCanSetInteractiveMode()
    {
        $mode = new Mode;
        $mode->setNonInteractive();

        $composer = $this->getPreparedComposerInstance([], '');
        $configuration = new Configuration($composer, $mode);

        $this->assertFalse($configuration->isInteractiveMode());
    }

    public function testCanGetCorrectComposerBinDirectory()
    {
        $binDir = __DIR__ . '/../../vendor/bin';

        $composer = $this->getPreparedComposerInstance([], $binDir);
        $configuration = new Configuration($composer, new Mode);

        $this->assertEquals($binDir, $configuration->getComposerBinDirectory());
    }

    public function testCanGetCorrectBinDir()
    {
        $composer = $this->getPreparedComposerInstance([], '');
        $configuration = new Configuration($composer, new Mode);

        $this->assertEquals(realpath(__DIR__ . '/../../bin'), $configuration->getBinDirectory());
    }

    /**
     * @param mixed $extra
     * @param mixed $binDir
     *
     * @return Composer
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
