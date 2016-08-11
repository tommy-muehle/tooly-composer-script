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
        $name = $tool->getName();
        $filename = $tool->getFilename();
        $url = $tool->getUrl();
        $signUrl = $tool->getSignUrl();

        $this->io->write(sprintf('<comment>Process tool "%s" ...</comment>', $name));

        if (false === $this->isDevMode && true === $tool->isOnlyDev()) {
            $this->io->write('<comment>
... skipped! Only installed in Dev mode.
</comment>');
            return;
        }

        if (false === $this->isAccessible($tool)) {
            $this->io->write('<error>
At least one given URL are not accessible!
</error>');
            return;
        }

        if (true === $this->helper->isFileAlreadyExist($filename, $url)) {
            $this->io->write(sprintf('<info>
File "%s" already exists in the given version.
</info>', $filename));
            return;
        }

        if (null !== $signUrl && false === $this->helper->isVerified($signUrl, $url)) {
            $this->io->write(sprintf('<error>
Verification failed! Please download the files manually and run the command 
$ gpg --verify --status-fd 1 /path/to/%s /path/to/%s
to get more details. In most cases you need to add the public key of the file author.
So please take a look at the documentation on 
> https://www.gnupg.org/gph/en/manual/book1.html
</error>', basename($signUrl), basename($url)));
            return;
        }

        if (file_exists($filename) && false === $this->doReplace($tool)) {
            $this->io->write('<info>
No replace selected. Skipped.
</info>');
            return;
        }

        $data = $this->helper->download($url);

        $this->helper->createFile($filename, $data);
        $this->helper->copyFile($filename, $filename . '.phar');

        $this->io->write(
            sprintf('<info>
File "%s" %s and copy "%s" are written!
</info>', $filename, PHP_EOL, $filename . '.phar')
        );
    }

    /**
     * @param Tool $tool
     *
     * @return bool
     */
    private function doReplace(Tool $tool)
    {
        $doReplace = $tool->forceReplace();

        if (true === $this->io->isInteractive()) {
            $this->io->write('<comment>Checksums are not equal!</comment>');
            $this->io->write(sprintf(
                '<comment>Do you want to overwrite the existing file "%s"?</comment>',
                $tool->getName()
            ));

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
