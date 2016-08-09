<?php

namespace Tooly\Tests\Script;

use Composer\IO\ConsoleIO;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Tooly\Exception\DownloadException;
use Tooly\Model\Tool;
use Tooly\Script\Helper\Downloader;
use Tooly\Script\Helper\Filesystem;
use Tooly\Script\Processor;

/**
 * @package Tooly\Tests\Script
 */
class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var StreamOutput
     */
    private $output;

    public function setUp()
    {
        $output = new StreamOutput(fopen('php://memory', 'w', false));

        // simulate an input for the question
        $input = fopen('php://memory', 'r+', false);
        fputs($input, 'no');
        rewind($input);

        $helperset = new HelperSet;
        $helperset->set(new QuestionHelper);
        $helperset->get('question')->setInputStream($input);

        $this->io = new ConsoleIO(new ArrayInput([]), $output, $helperset);
        $this->output = $output;
    }

    public function testEmptyToolsReturnsDocumentation()
    {
        vfsStream::setup();

        $processor = new Processor($this->io, new Filesystem, new Downloader);
        $processor->downloadTools([], 'vfs://root');

        $this->assertRegexp(
            '/No \"tools\" are found under the \"extra\" section in your composer\.json\!/',
            $this->getDisplay($this->output)
        );
    }

    public function testNoDevModeToolsAreSkipped()
    {
        vfsStream::setup();

        $processor = new Processor($this->io, new Filesystem, new Downloader, false);
        $processor->downloadTools(['tool' => ['url' => 'fake', 'only-dev' => true]], 'vfs://root');

        $this->assertRegexp(
            '/Only installed in Dev mode/',
            $this->getDisplay($this->output)
        );
    }

    /**
     * @expectedException \Tooly\Exception\DownloadException
     */
    public function testNonAccessibleUrlReturnsError()
    {
        vfsStream::setup();

        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->setMethods(['isAccessible'])
            ->getMock();

        $downloader
            ->method('isAccessible')
            ->willReturn(false);

        $processor = new Processor($this->io, new Filesystem, $downloader);
        $processor->downloadTools(['tool' => ['url' => 'fake']], 'vfs://root');
    }

    public function testAlreadyExistingFileReturnsNotice()
    {
        vfsStream::setup();

        $filesystem = $this
            ->getMockBuilder(Filesystem::class)
            ->setMethods(['isFileAlreadyExist', 'doVerify'])
            ->getMock();

        $filesystem
            ->method('isFileAlreadyExist')
            ->willReturn(true);

        $filesystem
            ->method('doVerify')
            ->willReturn(true);

        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->setMethods(['isAccessible'])
            ->getMock();

        $downloader
            ->method('isAccessible')
            ->willReturn(true);

        $processor = new Processor($this->io, $filesystem, $downloader);
        $processor->downloadTools(['tool' => ['url' => 'fake']], 'vfs://root');

        $this->assertRegexp(
            '/are already exist in given version/',
            $this->getDisplay($this->output)
        );
    }

    public function testAlreadyExistingFileButWrongVerificationReturnsQuestion()
    {
        vfsStream::setup();

        $filesystem = $this
            ->getMockBuilder(Filesystem::class)
            ->setMethods(['isFileAlreadyExist', 'doVerify'])
            ->getMock();

        $filesystem
            ->method('isFileAlreadyExist')
            ->willReturn(true);

        $filesystem
            ->method('doVerify')
            ->willReturn(false);

        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->setMethods(['isAccessible'])
            ->getMock();

        $downloader
            ->method('isAccessible')
            ->willReturn(true);

        $processor = new Processor($this->io, $filesystem, $downloader);
        $processor->downloadTools(['tool' => ['url' => 'fake']], 'vfs://root');

        $this->assertRegexp(
            '/Do you want to overwrite the existing file/',
            $this->getDisplay($this->output)
        );
    }

    public function testAlreadyExistingFileAndWrongVerificationButPreferSourceOverwrites()
    {
        vfsStream::setup();

        $filesystem = $this
            ->getMockBuilder(Filesystem::class)
            ->setMethods(['isFileAlreadyExist', 'doVerify'])
            ->getMock();

        $filesystem
            ->method('isFileAlreadyExist')
            ->willReturn(true);

        $filesystem
            ->method('doVerify')
            ->willReturn(false);

        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->setMethods(['isAccessible'])
            ->getMock();

        $downloader
            ->method('isAccessible')
            ->willReturn(true);

        $processor = new Processor($this->io, $filesystem, $downloader);
        $processor->downloadTools(['tool' => ['url' => 'fake']], 'vfs://root');

        $this->assertRegexp(
            '/Do you want to overwrite the existing file/',
            $this->getDisplay($this->output)
        );
    }

    /**
     * @expectedException \Tooly\Exception\DownloadException
     */
    public function testInvalidDownloadReturnsException()
    {
        vfsStream::setup();

        $filesystem = $this
            ->getMockBuilder(Filesystem::class)
            ->setMethods(['isFileAlreadyExist', 'doVerify'])
            ->getMock();

        $filesystem
            ->method('isFileAlreadyExist')
            ->willReturn(false);

        $filesystem
            ->method('doVerify')
            ->willReturn(false);

        $downloader = $this
            ->getMockBuilder(Downloader::class)
            ->setMethods(['isAccessible', 'download'])
            ->getMock();

        $downloader
            ->method('isAccessible')
            ->willReturn(true);

        $downloader
            ->method('download')
            ->willReturn(false);

        $processor = new Processor($this->io, $filesystem, $downloader);
        $processor->downloadTools(['tool' => ['url' => 'fake']], 'vfs://root');
    }

    public function testCanDownloadAStillNotExistingPharTool()
    {
        vfsStream::setup();

        $tools = [
            'php-metrics-monitor' => [
                'url' => 'https://github.com/tommy-muehle/php-metrics-monitor/releases/download/1.0.1/memo.phar',
                'only-dev' => true,
            ]
        ];

        $processor = new Processor($this->io, new Filesystem, new Downloader, true);
        $processor->downloadTools($tools, 'vfs://root/');

        $this->assertRegexp(
            '/are written\!/',
            $this->getDisplay($this->output)
        );
    }

    /**
     * @param StreamOutput $output
     *
     * @return string
     */
    private function getDisplay(StreamOutput $output)
    {
        rewind($output->getStream());

        return stream_get_contents($output->getStream());
    }
}
