<?php

namespace Tooly\Script\Helper;

use TM\GPG\Verification\Verifier as RealVerifier;
use TM\GPG\Verification\Exception\VerificationException;

/**
 * @package Script\Helper
 */
class Verifier
{
    /**
     * @param string $targetFilename
     * @param string $filename
     *
     * @return bool
     */
    public function checkSha1($targetFilename, $filename)
    {
        return sha1_file($targetFilename) === sha1_file($filename);
    }

    /**
     * @param string $signatureFile
     * @param string $file
     *
     * @return bool
     */
    public function checkGPG($signatureFile, $file)
    {
        if (false === class_exists(RealVerifier::class)) {
            return true;
        }

        $verifier = new RealVerifier;

        try {
            $verifier->verify($signatureFile, $file);
            return true;
        } catch (VerificationException $exception) {
            return false;
        }
    }
}
