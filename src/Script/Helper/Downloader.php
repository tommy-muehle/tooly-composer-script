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
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function isAccessible($url)
    {
        $context = StreamContextFactory::getContext($url, [
            'http' => ['timeout' => 5]
        ]);

        return is_resource(@fopen($url, 'r', null, $context));
    }

    /**
     * @param string $url
     *
     * @return string
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function download($url)
    {
        $context = StreamContextFactory::getContext($url);

        return file_get_contents($url, false, $context);
    }
}
