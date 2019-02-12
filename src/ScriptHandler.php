<?php

namespace Tooly;

use Composer\Script\Event;
use Tooly\Script\Configuration;
use Tooly\Script\Helper;
use Tooly\Script\Helper\Filesystem;
use Tooly\Script\Helper\Downloader;
use Tooly\Script\Helper\Verifier;
use Tooly\Script\Mode;
use Tooly\Script\Processor;
use TM\GPG\Verification\Verifier as GPGVerifier;

/**
 * @package Tooly
 */
class ScriptHandler
{
    /**
     * @param Event $event
     */
    public static function installPharTools(Event $event)
    {
        $gpgVerifier = null;
        $mode = new Mode;

        if (false === $event->isDevMode()) {
            $mode->setNoDev();
        }

        if (false === $event->getIO()->isInteractive()) {
            $mode->setNonInteractive();
        }

        $configuration = new Configuration($event->getComposer(), $mode);

        if (true === class_exists(GPGVerifier::class)) {
            $gpgVerifier = new GPGVerifier;
        }

        $helper = new Helper(new Filesystem, new Downloader, new Verifier($gpgVerifier));
        $processor = new Processor($event->getIO(), $helper, $configuration);

        $processor->cleanUp();

        foreach ($configuration->getTools() as $tool) {
            $processor->process($tool);
            $processor->symlinkOrCopy($tool);
        }
    }
}
