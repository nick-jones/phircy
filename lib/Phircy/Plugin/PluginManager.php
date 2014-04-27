<?php

namespace Phircy\Plugin;

/**
 * The PluginManager mainly deals with loading plugins from string based names.
 *
 * @package Phircy\Plugin
 */
class PluginManager
{
    /**
     * @var string[]|Plugin[]
     */
    protected $plugins;

    /**
     * @var string
     */
    protected $pluginsPath;

    /**
     * @param string[]|Plugin[] $plugins
     * @param string $pluginsPath
     */
    public function __construct($plugins, $pluginsPath)
    {
        $this->plugins = $plugins;
        $this->pluginsPath = $pluginsPath;
    }

    /**
     * Loads all unloaded plugins.
     */
    public function load()
    {
        foreach ($this->plugins as $i => $plugin) {
            // Plugins can be pre-loaded, if wanted. Also, it shouldn't be a problem calling load() multiple
            // times, particularly if we wish to allow plugins to be added after initial load.
            if ($plugin instanceof Plugin) {
                continue;
            }

            $this->plugins[$i] = $this->loadPlugin($plugin);
        }
    }

    /**
     * @return Plugin[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Loads a plugin by name, returning the appropriate instance, if available.
     *
     * @param string $pluginName
     * @return Plugin
     * @throws LoadException Triggered when the plugin cannot be located
     */
    protected function loadPlugin($pluginName)
    {
        $this->includePluginFile($pluginName);

        $class = sprintf('\Phircy\Plugins\%s', $pluginName);

        if (!class_exists($class)) {
            throw new LoadException(
                sprintf('Plugin class "%s" could not be resolved', $class),
                $pluginName
            );
        }

        return new $class();
    }

    /**
     * @param string $pluginName
     * @throws LoadException
     */
    protected function includePluginFile($pluginName)
    {
        $path = $this->pluginsPath . DIRECTORY_SEPARATOR . sprintf('%s.php', $pluginName);

        if (!is_readable($path)) {
            throw new LoadException(
                sprintf('Plugin file could not be found at location "%s"', $path),
                $pluginName
            );
        }

        require_once $path;
    }
}