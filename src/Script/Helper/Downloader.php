<?php

namespace Tooly\Script\Helper;

use Composer\Util\StreamContextFactory;

/**
 * @package Tooly\Script\Helper
 */
class Downloader
{
    /**
     * @param string $url
     *
     * @return bool
     */
    public function isAccessible($url)
    {
        $context = $this->getContext($url);

        return is_resource(@fopen($url, 'r', null, $context));
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function download($url)
    {
        $context = $this->getContext($url);

        return file_get_contents($url, false, $context);
    }

    /**
     * @param string $url
     *
     * @return resource
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function getContext($url)
    {
        return StreamContextFactory::getContext($url, [
            'http' => ['timeout' => 5]
        ]);
    }
}
