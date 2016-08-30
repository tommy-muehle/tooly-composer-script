<?php

namespace Tooly\Script;

use Tooly\Script\Helper\Verifier;
use Tooly\Script\Helper\Downloader;
use Tooly\Script\Helper\Filesystem;

/**
 * @package Tooly\Script
 */
class Helper
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Downloader
     */
    private $downloader;

    /**
     * @var Verifier
     */
    private $verifier;

    /**
     * @param Filesystem $filesystem
     * @param Downloader $downloader
     * @param Verifier   $verifier
     */
    public function __construct(Filesystem $filesystem, Downloader $downloader, Verifier $verifier)
    {
        $this->filesystem = $filesystem;
        $this->downloader = $downloader;
        $this->verifier   = $verifier;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function isAccessible($url)
    {
        return $this->downloader->isAccessible($url);
    }

    /**
     * @param string $filename
     * @param string $targetFile
     *
     * @return bool
     */
    public function isFileAlreadyExist($filename, $targetFile)
    {
        $alreadyExist = $this->filesystem->isFileAlreadyExist($filename);
        $verification = $this->verifier->checkFileSum($filename, $targetFile);

        if (true === $alreadyExist && true === $verification) {
            return true;
        }

        return false;
    }

    /**
     * @param string $signatureUrl
     * @param string $fileUrl
     *
     * @return bool
     */
    public function isVerified($signatureUrl, $fileUrl)
    {
        $data = $this->download($fileUrl);
        $signatureData = $this->download($signatureUrl);

        $tmpFile = rtrim(sys_get_temp_dir(), '/') . '/_tool';
        $this->createFile($tmpFile, $data);

        $tmpSignFile = rtrim(sys_get_temp_dir(), '/') . '/_tool.sign';
        $this->createFile($tmpSignFile, $signatureData);

        $result = $this->verifier->checkGPGSignature($tmpSignFile, $tmpFile);

        unlink($tmpFile);
        unlink($tmpSignFile);

        return $result;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function download($url)
    {
        return $this->downloader->download($url);
    }

    /**
     * @param string $filename
     * @param string $content
     *
     * @return bool
     */
    public function createFile($filename, $content)
    {
        return $this->filesystem->createFile($filename, $content);
    }

    /**
     * @param string $sourceFile
     * @param string $file
     *
     * @return bool
     */
    public function symlinkFile($sourceFile, $file)
    {
        return $this->filesystem->symlinkFile($sourceFile, $file);
    }
}
