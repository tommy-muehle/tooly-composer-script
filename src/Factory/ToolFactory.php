<?php

namespace Tooly\Factory;

use Tooly\Model\Tool;

/**
 * @package Factory
 */
class ToolFactory
{
    /**
     * @param string $name
     * @param string $directory
     * @param array  $parameters
     *
     * @return Tool
     */
    public static function createTool($name, $directory, array $parameters)
    {
        $defaults = [
            'url' => null,
            'sign-url' => null,
            'only-dev' => true,
            'force-replace' => false,
        ];

        $parameters = array_merge($defaults, $parameters);

        return new Tool(
            $name,
            $directory . DIRECTORY_SEPARATOR . $name,
            $parameters['url'],
            $parameters['sign-url'],
            $parameters['force-replace'],
            $parameters['only-dev']
        );
    }

    /**
     * @param string $directory
     * @param array  $data
     *
     * @return array
     */
    public static function createTools($directory, array $data)
    {
        $tools = [];

        foreach ($data as $name => $parameters) {
            $tools[] = self::createTool($name, $directory, $parameters);
        }

        return $tools;
    }
}
