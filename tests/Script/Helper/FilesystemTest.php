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

    public function testCanSymlinkAFile()
    {
        vfsStream::setup();

        file_put_contents('vfs://root/foo', 'test');

        $symlink = $this
            ->getFunctionMock('Tooly\Script\Helper', 'symlink')
            ->expects($this->once())
            ->willReturn(true);

        $filesystem = new Filesystem;
        $filesystem->symlinkFile('vfs://root/foo', 'vfs://root/bar');
    }

    public function testSymlinkFileToNonExistingDirectoryWorks()
    {
        $root = vfsStream::setup();

        $mkdir = $this
            ->getFunctionMock('Tooly\Script\Helper', 'mkdir')
            ->expects($this->once())
            ->willReturnCallback(function() use ($root) {
                $root->addChild(vfsStream::newDirectory('bar'));

                return true;
            });

        $symlink = $this
            ->getFunctionMock('Tooly\Script\Helper', 'symlink')
            ->expects($this->once());

        file_put_contents('vfs://root/foo.txt', 'foo');

        $filesystem = new Filesystem;
        $filesystem->symlinkFile('vfs://root/foo.txt', 'vfs://root/bar/bar.txt');
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

        $symlink = $this
            ->getFunctionMock('Tooly\Script\Helper', 'symlink')
            ->expects($this->never());

        $this->assertFalse($filesystem->createFile('vfs://root/foo/bar.txt', 'test'));
        $this->assertFalse($filesystem->symlinkFile('vfs://root/foo/bar.txt', 'vfs://root/foo/baz.txt'));
    }
}
