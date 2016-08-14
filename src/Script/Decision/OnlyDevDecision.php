<?php

namespace Tooly\Script\Decision;

use Tooly\Model\Tool;

/**
 * @package Tooly\Script\Decision
 */
class OnlyDevDecision extends AbstractDecision
{
    /**
     * @param Tool $tool
     *
     * @return bool
     */
    public function canProceed(Tool $tool)
    {
        if (false === $this->configuration->isDevMode() && true === $tool->isOnlyDev()) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return '<comment>... skipped! Only installed in Dev mode.</comment>';
    }
}
