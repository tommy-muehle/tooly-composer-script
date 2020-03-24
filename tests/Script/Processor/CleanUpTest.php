<?php

namespace Tooly\Tests\Script\Processor;

use Composer\IO\ConsoleIO;
use org\bovigo\vfs\vfsStream;
use Tooly\Script\Configuration;
use Tooly\Script\Helper;
use Tooly\Script\Processor;
use PHPUnit\Framework\TestCase;

/**
 * @package Tooly\Tests\Script
 */
class CleanupTest extends TestCase
{
    private $configuration;

    private $helper;

    private $root;

    protected function setUp()
    {
        $this->root = vfsStream::setup();

        mkdir(vfsStream::url('root/vendor/bin'), 0777, true);
        mkdir(vfsStream::url('root/bin'));

        $this->configuration = $this
            ->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configuration
            ->method('getComposerBinDirectory')
            ->willReturn(vfsStream::url('root/vendor/bin'));

        $this->configuration
            ->method('getBinDirectory')
            ->willReturn(vfsStream::url('root/bin'));

        $this->helper = $this
            ->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testEmptyDirectoryDoNothing()
    {
        $this->configuration
            ->method('getTools')
            ->willReturn([]);

        $this->helper
            ->expects($this->never())
            ->method('getFilesystem');

        $processor = $this->getProcessor();
        $processor->cleanUp();
    }

    public function testPharFileWasRemoved()
    {
        $this->root
            ->getChild('bin')
            ->addChild(vfsStream::newFile('tool.phar'));

        $this->configuration
            ->method('getTools')
            ->willReturn([]);

        $this->helper
            ->expects($this->never())
            ->method('getFilesystem');

        $processor = $this->getProcessor();
        $processor->cleanUp();
    }

    private function getProcessor()
    {
        return $this
            ->getMockBuilder(Processor::class)
            ->setConstructorArgs([
                $this->getMockBuilder(ConsoleIO::class)
                    ->disableOriginalConstructor()
                    ->getMock(),
                $this->helper,
                $this->configuration
            ])
            ->setMethodsExcept(['cleanUp'])
            ->getMock();
    }
}
