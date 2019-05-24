<?php

namespace Tooly\Script\Decision;

use Tooly\Model\Tool;

/**
 * @package Tooly\Script\Decision
 */
class FileAlreadyExistDecision extends AbstractDecision
{
    /**
     * @param Tool $tool
     *
     * @return bool
     */
    public function canProceed(Tool $tool)
    {
        $url = $tool->getUrl();

        if (false === $this->helper->getDownloader()->isAccessible($url)) {
            $url = $tool->getFallbackUrl();
        }

        if (false === $this->helper->isFileAlreadyExist($tool->getFilename(), $url)) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return '<info>File already exists in the given version.</info>';
    }
}
