<?php

namespace Phircy\Plugin;

/**
 * Exception to be triggered when plugin loading related issues occur.
 *
 * @package Phircy\Plugin
 */
class LoadException extends \RuntimeException
{
    /**
     * @var string
     */
    protected $pluginName;

    /**
     * @param string $message
     * @param string $pluginName
     */
    public function __construct($message, $pluginName)
    {
        $this->pluginName = $pluginName;

        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getPluginName()
    {
        return $this->pluginName;
    }
}