<?php

namespace Tooly\Script;

use Composer\Composer;
use Tooly\Factory\ToolFactory;

/**
 * @package Tooly\Script
 */
class Configuration
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var string
     */
    private $binDirectory;

    /**
     * @var string
     */
    private $composerBinDirectory;

    /**
     * @param Composer $composer
     * @param Mode     $mode
     */
    public function __construct(Composer $composer, Mode $mode)
    {
        $extras = $composer->getPackage()->getExtra();

        if (true === array_key_exists('tools', $extras)) {
            $this->data = array_merge([], $extras['tools']);
        }

        $this->mode = $mode;
        $this->binDirectory = realpath(__DIR__ . '/../../bin');
        $this->composerBinDirectory = $composer->getConfig()->get('bin-dir');
    }

    /**
     * @return bool
     */
    public function isDevMode()
    {
        return $this->mode->isDev();
    }

    /**
     * @return bool
     */
    public function isInteractiveMode()
    {
        return $this->mode->isInteractive();
    }

    /**
     * @return string
     */
    public function getBinDirectory()
    {
        return $this->binDirectory;
    }

    /**
     * @return string
     */
    public function getComposerBinDirectory()
    {
        return $this->composerBinDirectory;
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getTools()
    {
        return ToolFactory::createTools($this->binDirectory, $this->data);
    }
}
