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
     * @var bool
     */
    private $onlyDev = true;

    /**
     * @param string $name
     * @param string $filename
     * @param string $url
     * @param bool   $onlyDev
     */
    public function __construct($name, $filename, $url, $onlyDev = true)
    {
        $this->name = $name;
        $this->filename = $filename;
        $this->url = $url;
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
     * @return boolean
     */
    public function isOnlyDev()
    {
        return $this->onlyDev;
    }
}
