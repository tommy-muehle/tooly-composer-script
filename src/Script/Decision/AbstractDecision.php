<?php

namespace Tooly\Script\Decision;

use Tooly\Script\Configuration;
use Tooly\Script\Decision\DecisionInterface;
use Tooly\Script\Helper;

/**
 * @package Tooly\Script\Decision
 */
abstract class AbstractDecision implements DecisionInterface
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param Configuration $configuration
     * @param Helper        $helper
     */
    public function __construct(Configuration $configuration, Helper $helper)
    {
        $this->configuration = $configuration;
        $this->helper = $helper;
    }
}
