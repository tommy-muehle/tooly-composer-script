<?php

namespace Tooly\Script\Decision;

use Tooly\Model\Tool;

/**
 * @package Tooly\Script\Decision
 */
class UseFallbackURLDecision extends AbstractDecision
{
    /**
     * @param Tool $tool
     *
     * @return bool
     */
    public function canProceed(Tool $tool)
    {
        if (true === $this->helper->getDownloader()->isAccessible($tool->getUrl())) {
            return true;
        }

        if (empty($tool->getFallbackUrl())) {
            return true;
        }

        if (false === $this->helper->getDownloader()->isAccessible($tool->getFallbackUrl())){
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return '<error>Fallback URL is not accessible!</error>';
    }
}
