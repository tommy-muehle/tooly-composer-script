<?php

namespace Tooly\Script\Decision;

use Tooly\Model\Tool;

/**
 * @package Tooly\Script\Decision
 */
class IsAccessibleDecision extends AbstractDecision
{
    /**
     * @param Tool $tool
     *
     * @return bool
     */
    public function canProceed(Tool $tool)
    {
        if (false === $this->helper->isAccessible($tool->getUrl())) {
            return false;
        }

        if (empty($tool->getSignUrl())) {
            return true;
        }

        if (false === $this->helper->isAccessible($tool->getSignUrl())) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return '<error>At least one given URL are not accessible!</error>';
    }
}
