<?php

namespace Tooly\Script;

use Composer\IO\IOInterface;
use Tooly\Script\Decision\DecisionInterface;
use Tooly\Script\Decision\DoReplaceDecision;
use Tooly\Script\Decision\FileAlreadyExistDecision;
use Tooly\Script\Decision\IsAccessibleDecision;
use Tooly\Script\Decision\IsVerifiedDecision;
use Tooly\Script\Decision\OnlyDevDecision;
use Tooly\Script\Helper;
use Tooly\Model\Tool;

/**
 * @package Tooly\Script
 */
class Processor
{
    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param IOInterface   $io
     * @param Helper        $helper
     * @param Configuration $configuration
     */
    public function __construct(IOInterface $io, Helper $helper, Configuration $configuration)
    {
        $this->io = $io;
        $this->helper = $helper;
        $this->configuration = $configuration;
    }

    /**
     * @param Tool $tool
     */
    public function process(Tool $tool)
    {
        $this->io->write(sprintf('<comment>Process tool "%s" ...</comment>', $tool->getName()));

        /* @var $decision DecisionInterface */
        foreach ($this->getDecisions() as $decision) {
            if (true === $decision->canProceed($tool)) {
                continue;
            }

            $this->io->write($decision->getReason());
            return;
        }

        $data = $this->helper->download($tool->getUrl());
        $filename = $tool->getFilename();

        $this->helper->createFile($filename, $data);
        $this->helper->copyFile($filename, $filename . '.phar');

        $this->io->write(sprintf(
            '<info>File "%s" %s and copy "%s" are written!</info>',
            $filename,
            PHP_EOL,
            $filename . '.phar'
        ));
    }

    /**
     * Each decision can interrupt the download of a tool.
     *
     * @return array
     */
    private function getDecisions()
    {
        return [
            new OnlyDevDecision($this->configuration, $this->helper),
            new IsAccessibleDecision($this->configuration, $this->helper),
            new FileAlreadyExistDecision($this->configuration, $this->helper),
            new IsVerifiedDecision($this->configuration, $this->helper),
            new DoReplaceDecision($this->configuration, $this->helper, $this->io),
        ];
    }
}
