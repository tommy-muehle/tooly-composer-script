<?php

namespace Tooly\Tests\Script;

use Composer\IO\ConsoleIO;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Tooly\Model\Tool;
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

        $io = new ConsoleIO(new ArrayInput([]), $output, $helperset);

        $this->processor = new Processor($io);
        $this->output = $output;
    }

    public function testNonAccessibleUrlReturnsError()
    {
        $tool = $this
            ->getMockBuilder(Tool::class)
            ->setConstructorArgs(['test', 'test', ['url' => 'unreachable']])
            ->setMethods(['isUrlAccessible'])
            ->getMock();

        $tool
            ->method('isUrlAccessible')
            ->willReturn(false);

        $this->processor->downloadTool($tool);

        $this->assertRegexp(
            '/Sorry\! Cannot access \"unreachable"\!/',
            $this->getDisplay($this->output)
        );
    }

    public function testAlreadyExistingFileReturnsNotice()
    {
        $tool = $this
            ->getMockBuilder(Tool::class)
            ->setConstructorArgs(['test', 'test', []])
            ->setMethods(['isUrlAccessible', 'isFileAlreadyExisting', 'doVerify'])
            ->getMock();

        $tool
            ->method('isUrlAccessible')
            ->willReturn(true);

        $tool
            ->method('isFileAlreadyExisting')
            ->willReturn(true);

        $tool
            ->method('doVerify')
            ->willReturn(true);

        $this->processor->downloadTool($tool);

        $this->assertRegexp(
            '/File \"test\" already exist in given version\./',
            $this->getDisplay($this->output)
        );
    }

    public function testAlreadyExistingFileButWrongVerificationReturnsQuestion()
    {
        $tool = $this
            ->getMockBuilder(Tool::class)
            ->setConstructorArgs(['test', 'test', []])
            ->setMethods(['isUrlAccessible', 'isFileAlreadyExisting', 'doVerify'])
            ->getMock();

        $tool
            ->method('isUrlAccessible')
            ->willReturn(true);

        $tool
            ->method('isFileAlreadyExisting')
            ->willReturn(true);

        $tool
            ->method('doVerify')
            ->willReturn(false);

        $this->processor->downloadTool($tool);

        $this->assertRegexp(
            '/Checksums are not equal\!
Do you want to overwrite the existing file \"test\"\?
\[yes\] or \[no\]\?/',
            $this->getDisplay($this->output)
        );
    }

    public function testCanDownloadAStillNotExistingPharTool()
    {
        vfsStream::setup();

        $tool = new Tool('php-metrics-monitor', 'vfs://root/php-metrics-monitor', [
            'url' => 'https://github.com/tommy-muehle/php-metrics-monitor/releases/download/1.0.1/memo.phar',
            'only-dev' => true,
        ]);

        $this->processor->downloadTool($tool);

        $this->assertRegexp(
            '/written\./',
            $this->getDisplay($this->output)
        );
    }

    private function getDisplay(StreamOutput $output)
    {
        rewind($output->getStream());

        return stream_get_contents($output->getStream());
    }
}
