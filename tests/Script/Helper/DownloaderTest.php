<?php

namespace Tooly\Tests\Script\Helper;

use phpmock\phpunit\PHPMock;
use Tooly\Script\Helper\Downloader;
use PHPUnit\Framework\TestCase;

/**
 * @package Tooly\Tests\Script\Helper
 */
class DownloaderTest extends TestCase
{
    use PHPMock;

    public function testAccessibleTestWorksCorrect()
    {
        $downloader = new Downloader;

        $this->assertFalse($downloader->isAccessible('foo'));
        $this->assertTrue($downloader->isAccessible('https://github.com/tommy-muehle/tooly-composer-script/blob/master/README.md'));
    }

    public function testCanDownloadContentFromUrl()
    {
        $downloader = new Downloader;

        $this->assertRegExp(
            '/tooly-composer-script/',
            $downloader->download('https://github.com/tommy-muehle/tooly-composer-script/blob/master/README.md')
        );
    }
}
