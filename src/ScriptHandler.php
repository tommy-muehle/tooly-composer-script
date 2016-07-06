<?php

namespace Tooly;

use Composer\Script\Event;
use Tooly\Script\Helper\Filesystem;
use Tooly\Script\Helper\Downloader;
use Tooly\Script\Processor;
use Tooly\Model\Tool;

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
        $tools = [];

        $io = $event->getIO();
        $composer = $event->getComposer();
        $extras = $composer->getPackage()->getExtra();

        if (true === array_key_exists('tools', $extras)) {
            $tools = array_merge($tools, $extras['tools']);
        }

        try {
            $processor = new Processor($io, new Filesystem, new Downloader, $event->isDevMode());
            $processor->downloadTools($tools, $composer->getConfig()->get('bin-dir'));
        } catch (\Exception $exception) {
            $io->writeError($exception->getMessage());
        }
    }
}
