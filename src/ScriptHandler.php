<?php

namespace Tooly;

use Composer\Script\Event;
use Tooly\Script\Configuration;
use Tooly\Script\Helper;
use Tooly\Script\Helper\Filesystem;
use Tooly\Script\Helper\Downloader;
use Tooly\Script\Helper\Verifier;
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
        $configuration = new Configuration($event->getComposer(), $event->isDevMode(), $event->getIO()->isInteractive());

        if (true === class_exists(GPGVerifier::class)) {
            $gpgVerifier = new GPGVerifier;
        }

        $helper = new Helper(new Filesystem, new Downloader, new Verifier($gpgVerifier));
        $processor = new Processor($event->getIO(), $helper, $configuration);

        foreach ($configuration->getTools() as $tool) {
            $processor->process($tool);
        }
    }
}
