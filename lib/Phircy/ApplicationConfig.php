<?php

namespace Phircy;

/**
 * Configuration container for various pieces of injectable information.
 *
 * @package Phircy
 */
class ApplicationConfig extends \SimpleConfig\Container {
    /**
     * @param array $values
     * @param array $factories
     */
    public function __construct(array $values = array(), array $factories = array()) {
        $values += $this->createDefaultValues();
        $factories += $this->createDefaultFactories();

        parent::__construct($values, $factories);
    }

    /**
     * @return array
     */
    protected function createDefaultValues() {
        $values = array(
            'networks' => array(),
            'listeners' => array(),
            'subscribers' => array(),
            'plugins' => array(),
            'plugins_path' => implode(
                DIRECTORY_SEPARATOR,
                array(__DIR__, '..', '..', 'plugins')
            )
        );

        return $values;
    }

    /**
     * @return array
     */
    public function createDefaultFactories() {
        $factories = array(
            'irc.connection_factory' => function() {
                return new Connection\IrcFactory(
                    $this['core.transport_factory'],
                    $this['irc.generator']
                );
            },
            'irc.generator' => function() {
                return new \Phergie\Irc\Generator();
            },
            'irc.parser' => function() {
                return new Parser\PhergieIrcParser();
            },
            'core.event_dispatcher' => function() {
                return new \Symfony\Component\EventDispatcher\EventDispatcher();
            },
            'core.subscribers' => function() {
                return array(
                    new Application\Subscriber\ModelUpdatingSubscriber(),
                    new Application\Subscriber\OrchestratingSubscriber($this)
                );
            },
            'core.transport_factory' => function() {
                return new \Phipe\Connection\Stream\StreamFactory();
            },
            'core.plugin_manager' => function() {
                return new \Phircy\Plugin\PluginManager($this['plugins'], $this['plugins_path']);
            }
        );

        return $factories;
    }
}