<?php

namespace Tooly\Script;

use Composer\IO\IOInterface;
use Composer\Util\StreamContextFactory;
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
     * @param IOInterface $io
     */
    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    /**
     * @param Tool $tool
     */
    public function downloadTool(Tool $tool)
    {
        if (false === $tool->isUrlAccessible()) {
            $this->io->writeError(sprintf('Sorry! Cannot access "%s"!', $tool->getUrl()));

            return;
        }

        if (true === $tool->isFileAlreadyExisting() && true === $tool->doVerify()) {
            $this->io->write(sprintf('<info>File "%s" already exist in given version.</info>', $tool->getFilename()));

            return;
        }

        $doOverwrite = true;

        if (true === $tool->isFileAlreadyExisting() && false === $tool->doVerify()) {
            $this->io->write('<info>Checksums are not equal!</info>');
            $this->io->write(sprintf('<info>Do you want to overwrite the existing file "%s"?', $tool->getFilename()));

            $doOverwrite = $this->io->askConfirmation('<question>[yes] or [no]?</question>', false);
        }

        if (false === $doOverwrite) {
            return;
        }

        $filename = $tool->getFilename();
        $remoteFile = $tool->getUrl();

        if (false === $this->doDownload($filename, $remoteFile)) {
            $this->io->writeError(sprintf('Sorry! Could not download file "%s"!', $remoteFile));

            return;
        }

        $this->io->write(sprintf('<info>File "%s" written.</info>', $filename), true);
    }

    /**
     * @param string $filename
     * @param string $remoteFile
     *
     * @return bool
     */
    private function doDownload($filename, $remoteFile)
    {
        $context = StreamContextFactory::getContext($remoteFile);
        $data = file_get_contents($remoteFile, false, $context);

        if (false === $data) {
            return false;
        }

        $directory = dirname($filename);
        if (!file_exists($directory)) {
            @mkdir($directory, 0777, true);
        }

        file_put_contents($filename, $data);
        chmod($filename, 0755);

        return true;
    }
}
