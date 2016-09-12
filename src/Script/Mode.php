<?php

namespace Tooly\Script;

/**
 * @package Tooly\Script
 */
class Mode
{
    /**
     * @var bool
     */
    private $isDev = true;

    /**
     * @var bool
     */
    private $isInteractive = true;

    /**
     * Set flag for composer dev-mode to false.
     */
    public function setNoDev()
    {
        $this->isDev = false;
    }

    /**
     * Set flag for CLI interaction to false.
     */
    public function setNonInteractive()
    {
        $this->isInteractive = false;
    }

    /**
     * Returns if composer runs in dev-mode.
     *
     * @return bool
     */
    public function isDev()
    {
        return $this->isDev;
    }

    /**
     * Returns if the CLI can interact.
     *
     * @return bool
     */
    public function isInteractive()
    {
        return $this->isInteractive;
    }
}
