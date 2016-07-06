<?php

namespace Tooly\Exception;

/**
 * @package Tooly\Exception
 */
class DownloadException extends \Exception
{
    /**
     * @param string $url
     * @param string $reason
     *
     * @return static
     */
    public static function create($url, $reason)
    {
        return new static(sprintf('Problem with given url "%s" because: "%s"', $url, $reason));
    }

    /**
     * @param string $url
     *
     * @return DownloadException
     */
    public static function cannotAccess($url)
    {
        return static::create($url, 'Could not access!');
    }

    /**
     * @param string $url
     * 
     * @return DownloadException
     */
    public static function invalidDownload($url)
    {
        return static::create($url, 'Could not download content!');
    }
}
