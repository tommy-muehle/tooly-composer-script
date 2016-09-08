<?php

namespace Tooly\Tests\Script;

use phpmock\phpunit\PHPMock;
use org\bovigo\vfs\vfsStream;
use Tooly\Script\Helper;
use Tooly\Script\Helper\Filesystem;
use Tooly\Script\Helper\Downloader;
use Tooly\Script\Helper\Verifier;

/**
 * @package Tooly\Tests
 */
class HelperTest extends \PHPUnit_Framework_TestCase
{
    use PHPMock;

    public function testCanVerifyAFile()
    {
        vfsStream::setup();

        $this
            ->getFunctionMock('Tooly\Script', 'sys_get_temp_dir')
            ->expects($this->any())
            ->willReturn('vfs://root/');

        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->setMethods(['download'])
            ->getMock();

        $downloader
            ->expects($this->any())
            ->method('download')
            ->willReturn('foo');

        $verifier = $this
            ->getMockBuilder(Verifier::class)
            ->setMethods(['checkGPGSignature'])
            ->getMock();

        $verifier
            ->expects($this->exactly(2))
            ->method('checkGPGSignature')
            ->willReturnOnConsecutiveCalls(true, false);

        $helper = new Helper(new Filesystem, $downloader, $verifier);

        $this->assertTrue($helper->isVerified('foo.sign', 'foo'));
        $this->assertFalse(file_exists('vfs://root/_tool'));
        $this->assertFalse(file_exists('vfs://root/_tool.sign'));

        $this->assertFalse($helper->isVerified('foo.sign', 'foo'));
        $this->assertFalse(file_exists('vfs://root/_tool'));
        $this->assertFalse(file_exists('vfs://root/_tool.sign'));
    }

    public function testCanCheckIfFileAlreadyExist()
    {
        $filesystem = $this
            ->getMockBuilder(Filesystem::class)
            ->setMethods(['isFileAlreadyExist'])
            ->getMock();

        $filesystem
            ->expects($this->exactly(2))
            ->method('isFileAlreadyExist')
            ->willReturnOnConsecutiveCalls(true, false);

        $verifier = $this
            ->getMockBuilder(Verifier::class)
            ->setMethods(['checkFileSum'])
            ->getMock();

        $verifier
            ->expects($this->exactly(2))
            ->method('checkFileSum')
            ->willReturnOnConsecutiveCalls(true, false);

        $helper = new Helper($filesystem, new Downloader, $verifier);

        $this->assertTrue($helper->isFileAlreadyExist('foo', 'bar'));
        $this->assertFalse($helper->isFileAlreadyExist('foo', 'bar'));
    }

    public function testCanSymlinkAFile()
    {
        $filesystem = $this
            ->getMockBuilder(Filesystem::class)
            ->getMock();

        $filesystem
            ->expects($this->once())
            ->method('symlinkFile')
            ->willReturn(true);

        $helper = new Helper($filesystem, new Downloader, new Verifier);
        $this->assertTrue($helper->getFilesystem()->symlinkFile('foo', 'bar'));
    }
}
