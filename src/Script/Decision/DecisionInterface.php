<?php

namespace Tooly\Script\Decision;

use Tooly\Model\Tool;

/**
 * @package Tooly\Script\Decision
 */
interface DecisionInterface
{
    /**
     * @param Tool $tool
     *
     * @return bool
     */
    public function canProceed(Tool $tool);

    /**
     * @return string
     */
    public function getReason();
}
