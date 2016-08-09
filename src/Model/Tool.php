<?php

namespace Tooly\Model;

/**
 * @package Tooly\Model
 */
class Tool
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $signUrl;

    /**
     * @var bool
     */
    private $forceReplace = false;

    /**
     * @var bool
     */
    private $onlyDev = true;

    /**
     * @param string $name
     * @param string $filename
     * @param string $url
     * @param string $signUrl
     * @param bool   $forceReplace
     * @param bool   $onlyDev
     */
    public function __construct($name, $filename, $url, $signUrl = null, $forceReplace = false, $onlyDev = true)
    {
        $this->name = $name;
        $this->filename = $filename;
        $this->url = $url;
        $this->signUrl = $signUrl;
        $this->forceReplace = $forceReplace;
        $this->onlyDev = $onlyDev;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getSignUrl()
    {
        return $this->signUrl;
    }

    /**
     * @return boolean
     */
    public function isOnlyDev()
    {
        return $this->onlyDev;
    }

    /**
     * @return bool
     */
    public function forceReplace()
    {
        return $this->forceReplace;
    }
}
