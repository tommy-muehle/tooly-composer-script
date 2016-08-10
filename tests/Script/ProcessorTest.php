<?php

namespace Tooly\Tests\Script;

use Composer\IO\ConsoleIO;
use org\bovigo\vfs\vfsStream;
use phpmock\phpunit\PHPMock;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Tooly\Model\Tool;
use Tooly\Script\Helper;
use Tooly\Script\Processor;

/**
 * @package Tooly\Tests
 */
class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    use PHPMock;

    /**
     * @var ConsoleIO
     */
    private $io;

    /**
     * @var StreamOutput
     */
    private $output;

    public function setUp()
    {
        $output = new StreamOutput(fopen('php://memory', 'w', false));

        $helperset = new HelperSet;
        $helperset->set(new QuestionHelper);
        $helperset->get('question')->setInputStream(call_user_func(function() {
            // simulate an input for the question
            $input = fopen('php://memory', 'r+', false);
            fputs($input, 'no');
            rewind($input);

            return $input;
        }));

        $input = new ArrayInput([]);
        $input->setInteractive(false);

        $this->io = new ConsoleIO($input, $output, $helperset);
        $this->output = $output;
    }

    public function testOnlyDevToolInNoDevModeAreSkipped()
    {
        $helper = $this
            ->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOnlyDev'])
            ->getMock();

        $tool
            ->expects($this->once())
            ->method('isOnlyDev')
            ->willReturn(true);

        $processor = new Processor($this->io, $helper, false);
        $processor->process($tool);

        $this->assertRegexp('/skipped/', $this->getDisplay($this->output));
    }

    public function testNotAccessibleUrlReturnsError()
    {
        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $helper = $this
            ->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $helper
            ->expects($this->once())
            ->method('isAccessible')
            ->willReturn(false);

        $processor = new Processor($this->io, $helper);
        $processor->process($tool);

        $this->assertRegexp('/not accessible/', $this->getDisplay($this->output));
    }

    public function testNotAccessibleSignatureUrlReturnsError()
    {
        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSignUrl'])
            ->getMock();

        $tool
            ->method('getSignUrl')
            ->willReturn('url');

        $helper = $this
            ->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $helper
            ->method('isAccessible')
            ->willReturnOnConsecutiveCalls(true, false);

        $processor = new Processor($this->io, $helper);
        $processor->process($tool);

        $this->assertRegexp('/not accessible/', $this->getDisplay($this->output));
    }

    public function testNothingToDoIfFileAlreadyExistInVersion()
    {
        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tool
            ->method('getSignUrl')
            ->willReturn('url');

        $helper = $this
            ->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $helper
            ->method('isAccessible')
            ->willReturn(true);

        $helper
            ->expects($this->once())
            ->method('isFileAlreadyExist')
            ->willReturn(true);

        $processor = new Processor($this->io, $helper);
        $processor->process($tool);

        $this->assertRegexp('/already exist in given version/', $this->getDisplay($this->output));
    }

    public function testInValidSignatureCheckReturnsError()
    {
        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tool
            ->method('getSignUrl')
            ->willReturn('url');

        $helper = $this
            ->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $helper
            ->method('isAccessible')
            ->willReturn(true);

        $helper
            ->expects($this->once())
            ->method('isFileAlreadyExist')
            ->willReturn(false);

        $helper
            ->expects($this->once())
            ->method('isVerified')
            ->willReturn(false);

        $processor = new Processor($this->io, $helper);
        $processor->process($tool);

        $this->assertRegexp('/Verification failed/', $this->getDisplay($this->output));
    }

    public function testNoReplaceForceSkipProcess()
    {
        $fileExists = $this
            ->getFunctionMock('Tooly\Script', 'file_exists')
            ->expects($this->once())
            ->willReturn(true);

        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tool
            ->method('getSignUrl')
            ->willReturn('url');

        $tool
            ->expects($this->once())
            ->method('forceReplace')
            ->willReturn(false);

        $helper = $this
            ->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $helper
            ->method('isAccessible')
            ->willReturn(true);

        $helper
            ->expects($this->once())
            ->method('isFileAlreadyExist')
            ->willReturn(false);

        $helper
            ->expects($this->once())
            ->method('isVerified')
            ->willReturn(true);

        $processor = new Processor($this->io, $helper);
        $processor->process($tool);

        $this->assertRegexp('/No replace selected/', $this->getDisplay($this->output));
    }

    public function testCanDownloadATool()
    {
        vfsStream::setup();

        $tool = $this
            ->getMockBuilder(Tool::class)
            ->disableOriginalConstructor()
            ->getMock();

        $tool
            ->method('getSignUrl')
            ->willReturn('url');

        $tool
            ->method('forceReplace')
            ->willReturn(true);

        $tool
            ->method('getFilename')
            ->willReturn('vfs://root/tool');

        $tool
            ->method('getName')
            ->willReturn('tool');

        $helper = $this
            ->getMockBuilder(Helper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $helper
            ->method('isAccessible')
            ->willReturn(true);

        $helper
            ->expects($this->once())
            ->method('isFileAlreadyExist')
            ->willReturn(false);

        $helper
            ->expects($this->once())
            ->method('isVerified')
            ->willReturn(true);

        $helper
            ->expects($this->once())
            ->method('download')
            ->willReturn('download-content');

        $processor = new Processor($this->io, $helper);
        $processor->process($tool);

        $this->assertRegexp('/are written/', $this->getDisplay($this->output));
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
