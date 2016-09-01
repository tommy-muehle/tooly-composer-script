<?php

namespace Tooly\Script\Helper;

use Composer\Util\Filesystem as ComposerFileSystem;

/**
 * @package Tooly\Script\Helper
 */
class Filesystem
{
    /**
     * @var ComposerFileSystem
     */
    private $filesystem;

    /**
     * @param ComposerFileSystem|null $filesystem
     */
    public function __construct(ComposerFileSystem $filesystem = null)
    {
        $this->filesystem = $filesystem ?: new ComposerFileSystem();
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public function isFileAlreadyExist($filename)
    {
        return file_exists($filename);
    }

    /**
     * @param string $filename
     * @param string $content
     *
     * @return bool
     */
    public function createFile($filename, $content)
    {
        if (false === $this->createDirectory($filename)) {
            return false;
        }

        file_put_contents($filename, $content);
        chmod($filename, 0755);

        return true;
    }

    /**
     * @param string $sourceFile
     * @param string $file
     *
     * @return bool
     */
    public function symlinkFile($sourceFile, $file)
    {
        if (false === $this->createDirectory($file)) {
            return false;
        }

        if (true === $this->isFileAlreadyExist($file)) {
            return true;
        }

        return $this->filesystem->relativeSymlink($sourceFile, $file);
    }

    /**
     * @param string $directory
     *
     * @return bool
     */
    public function removeDirectory($directory)
    {
        return $this->filesystem->removeDirectoryPhp($directory);
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    private function createDirectory($filename)
    {
        $directory = dirname($filename);

        if (true === file_exists($directory)) {
            return true;
        }

        return mkdir($directory, 0777, true);
    }
}
