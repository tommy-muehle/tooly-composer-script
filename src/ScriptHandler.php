<?php

namespace Tooly;

use Composer\Script\Event;
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
        $composer = $event->getComposer();
        $extras = $composer->getPackage()->getExtra();

        if (false === array_key_exists('tools', $extras)) {
            return;
        }

        $binDirectory = $composer->getConfig()->get('bin-dir');
        $processor = new Processor($event->getIO());

        foreach ($extras['tools'] as $name => $parameters) {

            $parameters = array_merge(['url' => null, 'only-dev' => true], (array) $parameters);
            $filename = $binDirectory . '/' . $name;

            $tool = new Tool($name, $filename, $parameters);

            $event->getIO()->write(sprintf('Process tool "%s" ...', $tool->getName()));

            if (false === $event->isDevMode() && true === $tool->isOnlyForDevMode()) {
                $event->getIO()->write(sprintf('<info>Skipped! "%s" should be only installed in Dev mode.</info>', $tool->getName()));
                continue;
            }

            $processor->downloadTool($tool);
        }
    }
}
