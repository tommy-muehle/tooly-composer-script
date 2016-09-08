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
        $data = $this->downloader->download($fileUrl);
        $signatureData = $this->downloader->download($signatureUrl);

        $tmpFile = rtrim(sys_get_temp_dir(), '/') . '/_tool';
        $this->filesystem->createFile($tmpFile, $data);

        $tmpSignFile = rtrim(sys_get_temp_dir(), '/') . '/_tool.sign';
        $this->filesystem->createFile($tmpSignFile, $signatureData);

        $result = $this->verifier->checkGPGSignature($tmpSignFile, $tmpFile);

        unlink($tmpFile);
        unlink($tmpSignFile);

        return $result;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @return Downloader
     */
    public function getDownloader()
    {
        return $this->downloader;
    }

    /**
     * @return Verifier
     */
    public function getVerifier()
    {
        return $this->verifier;
    }
}
