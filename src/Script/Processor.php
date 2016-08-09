<?php

namespace Tooly\Script;

use Composer\IO\IOInterface;
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
     * @var bool
     */
    private $isDevMode = true;

    /**
     * @param IOInterface $io
     * @param Helper      $helper
     * @param bool        $isDevMode
     */
    public function __construct(IOInterface $io, Helper $helper, $isDevMode = true)
    {
        $this->io = $io;
        $this->helper = $helper;
        $this->isDevMode = $isDevMode;
    }

    /**
     * @param Tool $tool
     */
    public function process(Tool $tool)
    {
        $filename = $tool->getFilename();
        $url = $tool->getUrl();
        $signUrl = $tool->getSignUrl();

        $this->io->write(sprintf('<comment>Process tool "%s" ... </comment>', $filename));

        if (false === $this->isDevMode && true === $tool->isOnlyDev()) {
            $this->io->write('<comment>... skipped! Only installed in Dev mode.</comment>');
            return;
        }

        if (false === $this->isAccessible($tool)) {
            $this->io->write('<error>FOO</error>');
            return;
        }

        if (true === $this->helper->isFileAlreadyExist($filename, $url)) {
            $this->io->write(sprintf('<info>File "%s" are already exist in given version.</info>', $filename));
            return;
        }

        if ($signUrl !== '' && false === $this->helper->isVerified($signUrl, $url)) {
            $this->io->write('<error>BAR</error>');
            return;
        }

        if (false === $this->doReplace($tool)) {
            $this->io->write('<info>No replace selected. Skipped.</info>');
            return;
        }

        $data = $this->helper->download($url);

        $this->helper->createFile($filename, $data);
        $this->helper->copyFile($filename, $filename . '.phar');

        $this->io->write(
            sprintf('<info>File "%s" %s and copy "%s" are written!</info>', $filename, PHP_EOL, $filename . '.phar')
        );
    }

    /**
     * @param Tool $tool
     *
     * @return bool
     */
    private function doReplace(Tool $tool)
    {
        if (false === $this->io->isInteractive() && true === $tool->forceReplace()) {
            return true;
        }

        $doReplace = false;

        if (true === $this->io->isInteractive()) {
            $this->io->write('<comment>Checksums are not equal!</comment>');
            $this->io->write(sprintf('<comment>Do you want to overwrite the existing file "%s"?</comment>', $tool->getName()));

            $doReplace = $this->io->askConfirmation('<question>[yes] or [no]?</question>', false);
        }

        return $doReplace;
    }

    /**
     * @param Tool $tool
     *
     * @return bool
     */
    private function isAccessible(Tool $tool)
    {
        if (false === $this->helper->isAccessible($tool->getUrl())) {
            return false;
        }

        if (empty($tool->getSignUrl())) {
            return true;
        }

        if (false === $this->helper->isAccessible($tool->getSignUrl())) {
            return false;
        }

        return true;
    }
}
