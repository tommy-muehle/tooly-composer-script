<?php

namespace Tooly\Script;

use Composer\IO\IOInterface;
use Tooly\Exception\DownloadException;
use Tooly\Factory\ToolFactory;
use Tooly\Script\Helper\Downloader;
use Tooly\Script\Helper\Filesystem;
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
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Downloader
     */
    private $downloader;

    /**
     * @var bool
     */
    private $isDevMode = true;

    /**
     * @param IOInterface $io
     * @param Filesystem  $filesystem
     * @param Downloader  $downloader
     * @param bool        $isDevMode
     */
    public function __construct(IOInterface $io, Filesystem $filesystem, Downloader $downloader, $isDevMode = true)
    {
        $this->io = $io;
        $this->filesystem = $filesystem;
        $this->downloader = $downloader;
        $this->isDevMode = $isDevMode;
    }

    /**
     * @param array  $tools
     * @param string $directory
     */
    public function downloadTools(array $tools, $directory)
    {
        if (0 === count($tools)) {
            $this->io->write(<<<EOT
No "tools" are found under the "extra" section in your composer.json!
Here an example:

...
"extra":
    "tools": {
       "php-metrics-monitor": {
         "url": "https://github.com/tommy-muehle/php-metrics-monitor/releases/download/1.0.1/memo.phar",
         "only-dev": true
       },
       ...
    }
...
EOT
            );
        }

        foreach ($tools as $name => $parameters) {
            $this->io->write(sprintf('<comment>Process tool "%s" ... </comment>', $name));
            $tool = ToolFactory::createTool($name, $directory, $parameters);

            if (false === $this->isDevMode && true === $tool->isOnlyDev()) {
                $this->io->write('<comment>... skipped! Only installed in Dev mode.</comment>');
                continue;
            }

            $this->downloadTool($tool);
            $this->io->write('<comment>... processed!</comment>');
        }
    }

    /**
     * @param Tool $tool
     */
    private function downloadTool(Tool $tool)
    {
        $url = $tool->getUrl();
        $filename = $tool->getFilename();

        if (false === $this->downloader->isAccessible($url)) {
            throw DownloadException::cannotAccess($url);
        }

        $alreadyExist = $this->filesystem->isFileAlreadyExist($filename);
        $verification = $this->filesystem->doVerify($filename, $url);

        if (true === $alreadyExist && true === $verification) {
            $this->io->write(sprintf('<info>File "%s" are already exist in given version.</info>', $filename));
            return;
        }

        $doOverwrite = false;

        if (false === $alreadyExist) {
            $doOverwrite = true;
        }

        if (true === $this->io->isInteractive() && true === $alreadyExist && false === $verification) {
            $this->io->write('<comment>Checksums are not equal!</comment>');
            $this->io->write(sprintf('<comment>Do you want to overwrite the existing file "%s"?</comment>', $filename));

            $doOverwrite = $this->io->askConfirmation('<question>[yes] or [no]?</question>', false);
        }

        if (false === $doOverwrite) {
            $this->io->write('<info>No overwrite selected. Skipped.</info>');
            return;
        }

        $data = $this->downloader->download($url);

        if (false === $data) {
            throw DownloadException::invalidDownload($url);
        }

        $this->filesystem->createFile($filename, $data);
        $this->filesystem->copyFile($filename, $filename . '.phar');

        $this->io->write(
            sprintf('<info>File "%s" %s and copy "%s" are written!</info>', $filename, PHP_EOL, $filename . '.phar')
        );
    }
}
