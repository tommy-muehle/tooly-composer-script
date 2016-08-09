<?php

namespace Tooly\Script\Helper;

/**
 * @package Tooly\Script\Helper
 */
class Filesystem
{
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
    public function copyFile($sourceFile, $file)
    {
        if (false === $this->createDirectory($file)) {
            return false;
        }

        if (true === $this->isFileAlreadyExist($file)) {
            return true;
        }

        return copy($sourceFile, $file);
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
