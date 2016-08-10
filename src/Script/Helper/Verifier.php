<?php

namespace Tooly\Script\Helper;

use TM\GPG\Verification\Verifier as GPGVerifier;
use TM\GPG\Verification\Exception\VerificationException;

/**
 * @package Script\Helper
 */
class Verifier
{
    /**
     * @var GPGVerifier
     */
    private $gpgVerifier;

    /**
     * @param GPGVerifier|null $gpgVerifier
     */
    public function __construct(GPGVerifier $gpgVerifier = null)
    {
        $this->gpgVerifier = $gpgVerifier;
    }

    /**
     * @param string $targetFilename
     * @param string $filename
     *
     * @return bool
     */
    public function checkFileSum($targetFilename, $filename)
    {
        if (!file_exists($targetFilename)) {
            return false;
        }

        return sha1_file($targetFilename) === sha1_file($filename);
    }

    /**
     * @param string $signatureFile
     * @param string $file
     *
     * @return bool
     */
    public function checkGPGSignature($signatureFile, $file)
    {
        if (!$this->gpgVerifier instanceof GPGVerifier) {
            return true;
        }

        try {
            $this->gpgVerifier->verify($signatureFile, $file);
            return true;
        } catch (VerificationException $exception) {
            return false;
        }
    }
}
