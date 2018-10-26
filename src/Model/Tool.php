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
     * @var bool
     */
    private $rename = false;

    /**
     * @param string $name
     * @param string $filename
     * @param string $url
     * @param string $signUrl
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct($name, $filename, $url, $signUrl = null)
    {
        $this->name = $name;
        $this->filename = $filename;
        $this->url = $url;
        $this->signUrl = $signUrl;
    }

    /**
     * @return void
     */
    public function activateForceReplace()
    {
        $this->forceReplace = true;
    }

    /**
     * @return void
     */
    public function disableOnlyDev()
    {
        $this->onlyDev = false;
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

    /**
     * @return void
     */
    public function setNameToToolKey()
    {
        $this->rename = true;
    }

    /**
     * @return bool
     */
    public function renameToConfigKey()
    {
        return $this->rename;
    }
}
