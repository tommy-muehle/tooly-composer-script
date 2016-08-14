<?php

namespace Tooly\Script\Decision;

use Tooly\Model\Tool;

/**
 * @package Tooly\Script\Decision
 */
class IsVerifiedDecision extends AbstractDecision
{
    /**
     * @param Tool $tool
     *
     * @return bool
     */
    public function decide(Tool $tool)
    {
        if (null === $tool->getSignUrl()) {
            return true;
        }

        return $this->helper->isVerified($tool->getSignUrl(), $tool->getUrl());
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return '<error>
Verification failed! Please download the files manually and run the command 
$ gpg --verify --status-fd 1 /path/to/tool.phar.sign /path/to/tool.phar
to get more details. In most cases you need to add the public key of the file author.
So please take a look at the documentation on 
> https://www.gnupg.org/gph/en/manual/book1.html
</error>';
    }
}
