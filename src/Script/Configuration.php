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
     * @var bool
     */
    private $isDevMode = true;

    /**
     * @var bool
     */
    private $isInteractiveMode = true;

    /**
     * @param Composer $composer
     * @param bool     $isDevMode
     * @param bool     $isInteractiveMode
     */
    public function __construct(Composer $composer, $isDevMode = true, $isInteractiveMode = true)
    {
        $extras = $composer->getPackage()->getExtra();

        if (true === array_key_exists('tools', $extras)) {
            $this->data = array_merge([], $extras['tools']);
        }

        $this->binDirectory = realpath(__DIR__ . '/../../bin');
        $this->composerBinDirectory = $composer->getConfig()->get('bin-dir');
        $this->isDevMode = $isDevMode;
        $this->isInteractiveMode = $isInteractiveMode;
    }

    /**
     * @return bool
     */
    public function isDevMode()
    {
        return $this->isDevMode;
    }

    /**
     * @return bool
     */
    public function isInteractiveMode()
    {
        return $this->isInteractiveMode;
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
     */
    public function getTools()
    {
        return ToolFactory::createTools($this->binDirectory, $this->data);
    }
}
