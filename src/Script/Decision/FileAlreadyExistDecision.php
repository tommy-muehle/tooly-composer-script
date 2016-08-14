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
        if (false === $this->helper->isFileAlreadyExist($tool->getFilename(), $tool->getUrl())) {
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
