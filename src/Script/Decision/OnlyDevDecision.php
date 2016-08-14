<?php

namespace Tooly\Script\Decision;

use Tooly\Model\Tool;

/**
 * @package Tooly\Script\Decision
 */
class OnlyDevDecision extends AbstractDecision
{
    public function decide(Tool $tool)
    {
        if (false === $this->configuration->isDevMode() && true === $tool->isOnlyDev()) {
            return false;
        }

        return true;
    }

    public function getReason()
    {
        return '<comment>... skipped! Only installed in Dev mode.</comment>';
    }
}
