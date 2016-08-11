<?php

namespace Tooly\Tests\Script\Helper;

use org\bovigo\vfs\vfsStream;
use phpmock\phpunit\PHPMock;
use Tooly\Script\Helper\Filesystem;

/**
 * @package Tooly\Tests\Script\Helper
 */
class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    use PHPMock;

    public function testCanCopyAFile()
    {
        vfsStream::setup();

        file_put_contents('vfs://root/foo', 'test');

        $filesystem = new Filesystem;
        $filesystem->copyFile('vfs://root/foo', 'vfs://root/bar');

        $this->assertEquals('test', file_get_contents('vfs://root/bar'));
    }

    public function testAlreadyExistingFileDoesntCopy()
    {
        vfsStream::setup();

        file_put_contents('vfs://root/foo', 'foo');
        file_put_contents('vfs://root/bar', 'bar');

        $filesystem = new Filesystem;
        $filesystem->copyFile('vfs://root/foo', 'vfs://root/bar');

        $this->assertEquals('bar', file_get_contents('vfs://root/bar'));
    }

    public function testCopyFileToNonExistingDirectoryWorks()
    {
        $root = vfsStream::setup();

        $mkdir = $this
            ->getFunctionMock('Tooly\Script\Helper', 'mkdir')
            ->expects($this->once())
            ->willReturnCallback(function() use ($root) {
                $root->addChild(vfsStream::newDirectory('bar'));

                return true;
            });

        file_put_contents('vfs://root/foo.txt', 'foo');

        $filesystem = new Filesystem;
        $filesystem->copyFile('vfs://root/foo.txt', 'vfs://root/bar/bar.txt');

        $this->assertEquals('foo', file_get_contents('vfs://root/bar/bar.txt'));
    }

    public function testCannotCreateDirectoryReturnsFalse()
    {
        $root = vfsStream::setup();
        $filesystem = new Filesystem;

        $mkdir = $this
            ->getFunctionMock('Tooly\Script\Helper', 'mkdir')
            ->expects($this->exactly(2))
            ->willReturnCallback(function() {
                return false;
            });

        $this->assertFalse($filesystem->createFile('vfs://root/foo/bar.txt', 'test'));
        $this->assertFalse($filesystem->copyFile('vfs://root/foo/bar.txt', 'vfs://root/foo/baz.txt'));
    }
}
