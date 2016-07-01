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
     * @var array
     */
    private $parameters = [];

    /**
     * @param string $name
     * @param string $filename
     * @param array  $parameters
     */
    public function __construct($name, $filename, array $parameters)
    {
        $this->name = $name;
        $this->filename = $filename;
        $this->parameters = $parameters;
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
     * @return string|null
     */
    public function getUrl()
    {
        return $this->parameters['url'];
    }

    /**
     * @return bool
     */
    public function isUrlAccessible()
    {
        return is_resource(@fopen($this->parameters['url'], 'r'));
    }

    /**
     * @return bool
     */
    public function isOnlyForDevMode()
    {
        return $this->parameters['only-dev'];
    }

    /**
     * @return bool
     */
    public function isFileAlreadyExisting()
    {
        return file_exists($this->filename);
    }

    /**
     * @return bool
     */
    public function doVerify()
    {
        return sha1_file($this->filename) === sha1_file($this->parameters['url']);
    }
}
