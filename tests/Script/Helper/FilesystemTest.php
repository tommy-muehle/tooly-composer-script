<?php

namespace Tooly\Tests\Script\Helper;

use Composer\Util\Platform;
use Tooly\Script\Helper\Filesystem;

/**
 * @package Tooly\Tests\Script\Helper
 */
class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $testDirectory;

    /**
     * @var string
     */
    private $testFile;

    public function setUp()
    {
        $this->filesystem = new Filesystem;
        $this->testDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test';
        $this->testFile = $this->testDirectory . DIRECTORY_SEPARATOR . 'file';
    }

    public function tearDown()
    {
        if (is_dir($this->testDirectory)) {
            $this->filesystem->removeDirectory($this->testDirectory);
        }
    }

    public function testCanRelativeSymlinkAFile()
    {
        if (Platform::isWindows()) {
            mkdir($this->testDirectory, 0777, true);
            file_put_contents($this->testFile, '');
        }
        $symlink = $this->testDirectory . DIRECTORY_SEPARATOR . '/foo/symlink';

        $this->assertTrue($this->filesystem->symlinkFile($this->testFile, $symlink));
        if (Platform::isWindows()) {
            $this->assertTrue(file_exists($symlink));
        } else {
            $this->assertNotEquals('/', substr(readlink($symlink), '0', 1));
        }
    }
}
