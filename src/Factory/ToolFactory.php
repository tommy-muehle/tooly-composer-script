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
        $parameters = array_merge(['url' => null, 'only-dev' => true], $parameters);

        return new Tool(
            $name,
            $directory . DIRECTORY_SEPARATOR . $name,
            $parameters['url'],
            $parameters['only-dev']
        );
    }
}
