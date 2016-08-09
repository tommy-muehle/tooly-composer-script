<?php

namespace Tooly;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Tooly\Factory\ToolFactory;
use Tooly\Script\Helper;
use Tooly\Script\Helper\Filesystem;
use Tooly\Script\Helper\Downloader;
use Tooly\Script\Helper\Verifier;
use Tooly\Script\Processor;

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
        $data = [];

        $composer = $event->getComposer();
        $extras = $composer->getPackage()->getExtra();

        if (true === array_key_exists('tools', $extras)) {
            $data = array_merge($data, $extras['tools']);
        }

        $tools = ToolFactory::createTools(
            $event->getComposer()->getConfig()->get('bin-dir'),
            $data
        );

        $helper = new Helper(new Filesystem, new Downloader, new Verifier);
        $processor = new Processor($event->getIO(), $helper, $event->isDevMode());

        foreach ($tools as $tool) {
            $processor->process($tool);
        }
    }
}
