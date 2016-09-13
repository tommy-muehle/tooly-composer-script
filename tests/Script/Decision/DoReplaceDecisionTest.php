<?php

namespace Tooly\Tests\Script\Decision;

use Composer\IO\ConsoleIO;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Tooly\Factory\ToolFactory;
use Tooly\Script\Decision\DoReplaceDecision;
use Tooly\Script\Helper\Filesystem;

/**
 * @package Tooly\Tests\Script\Decision
 */
class DoReplaceDecisionTest extends DecisionTestCase
{
    private $io;

    private $input;

    private $output;

    /**
     * @var HelperSet
     */
    private $helperSet;

    public function setUp()
    {
        parent::setUp();

        $this->input = new ArrayInput([]);
        $this->output = new StreamOutput(fopen('php://memory', 'w', false));
        $this->helperSet = new HelperSet;

        $this->io = new ConsoleIO($this->input, $this->output, $this->helperSet);
    }

    public function testIfFileNotExistReturnsTrue()
    {
        $filesystem = $this
            ->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $filesystem
            ->expects($this->once())
            ->method('isFileAlreadyExist')
            ->willReturn(false);

        $this->helper
            ->expects($this->once())
            ->method('getFilesystem')
            ->willReturn($filesystem);

        $decision = new DoReplaceDecision($this->configuration, $this->helper, $this->io);
        $tool = ToolFactory::createTool('tool', __DIR__, []);

        $this->assertTrue($decision->canProceed($tool));
    }

    public function testIfFileExistReturnsForceReplaceValue()
    {
        $filesystem = $this
            ->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $filesystem
            ->expects($this->exactly(2))
            ->method('isFileAlreadyExist')
            ->willReturn(true);

        $this->helper
            ->expects($this->exactly(2))
            ->method('getFilesystem')
            ->willReturn($filesystem);

        $decision = new DoReplaceDecision($this->configuration, $this->helper, $this->io);

        $tool = ToolFactory::createTool('tool', __DIR__, []);
        $this->assertFalse($decision->canProceed($tool));

        $tool->activateForceReplace();
        $this->assertTrue($decision->canProceed($tool));
    }

    public function testInteractiveModeWithAnswerReturnsThatValue()
    {
        $sayNo = fopen('php://memory', 'r+', false);
        fputs($sayNo, 'no');
        rewind($sayNo);

        $this->helperSet->set(new QuestionHelper);
        $this->helperSet
            ->get('question')
            ->setInputStream($sayNo);

        $filesystem = $this
            ->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $filesystem
            ->expects($this->exactly(2))
            ->method('isFileAlreadyExist')
            ->willReturn(true);

        $this->helper
            ->expects($this->exactly(2))
            ->method('getFilesystem')
            ->willReturn($filesystem);

        $this->configuration
            ->method('isInteractiveMode')
            ->willReturn(true);

        $decision = new DoReplaceDecision($this->configuration, $this->helper, $this->io);
        $tool = ToolFactory::createTool('tool', __DIR__, []);

        $this->assertFalse($decision->canProceed($tool));

        $sayYes = fopen('php://memory', 'r+', false);
        fputs($sayYes, 'yes');
        rewind($sayYes);

        $this->helperSet
            ->get('question')
            ->setInputStream($sayYes);

        $this->assertTrue($decision->canProceed($tool));
    }

    public function testCanGetReason()
    {
        $decision = new DoReplaceDecision($this->configuration, $this->helper, $this->io);
        $this->assertRegExp('/info/', $decision->getReason());
    }
}
