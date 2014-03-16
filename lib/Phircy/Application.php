<?php

namespace Phircy;

use Phircy\Application\ConfigModelMapper;
use Phircy\Application\TransportObserver;
use Phircy\Connection\IrcFactory;
use Phircy\Connection\IrcTransport;
use Phircy\Event\EventFactory;
use Phircy\Handler\DispatchingUpdateHandler;
use Phircy\Handler\PrintingUpdateHandler;
use Phircy\Model\Network;
use Phircy\Parser\IrcParser;
use Phircy\Plugin\PluginManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Main Phircy application class. This consumes the config to construct everything relevant to execute and orchestrate
 * the IRC sessions.
 *
 * @package Phircy
 */
class Application {
    /**
     * @var array
     */
    protected $config;

    /**
     * @var PluginManager
     */
    protected $pluginManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Phipe\Application
     */
    protected $phipe;

    /**
     * @var ConfigModelMapper
     */
    protected $configModelMapper;

    /**
     * @param array|ApplicationConfig|null $config $config
     * @param \Phipe\Application $phipe
     * @param ConfigModelMapper $mapper
     */
    public function __construct($config = NULL, \Phipe\Application $phipe = NULL, ConfigModelMapper $mapper = NULL) {
        $this->setConfig($config);
        $this->setPhipe($phipe ?: new \Phipe\Application());
        $this->setConfigModelMapper($mapper ?: new ConfigModelMapper());
    }

    /**
     * Main application run method.
     */
    public function execute() {
        $this->pluginManager = $this->getPluginManager();
        $this->pluginManager->load();

        $this->eventDispatcher = $this->getEventDispatcher();
        $this->prepareEventDispatcher($this->eventDispatcher);

        $this->eventDispatcher
            ->dispatch('core.start');

        $phipe = $this->getPhipe();
        $phipe->setConfig($this->createPhipeConfiguration());
        $phipe->execute();

        $this->eventDispatcher
            ->dispatch('core.exit');
    }

    /**
     * Creates configuration to be consumed by Phipe. All connections, observers and factories are constructed here.
     *
     * @return \SplObjectStorage|array
     */
    protected function createPhipeConfiguration() {
        $connections = $this->createConnectionsFromConfig();
        $factory = $this->getIrcConnectionFactory();

        return array(
            'connections' => $this->createTransportsFromConnections($factory, $connections),
            'observers' => $this->createTransportObservers($connections),
            'factory' => $factory
        );
    }

    /**
     * Create our internal transport observers. This needs a little more control, as the PrintingHandler should
     * be configurable.
     *
     * @param \SplObjectStorage|\Phircy\Model\Connection[] $connections
     * @return array
     */
    protected function createTransportObservers($connections) {
        return array(
            new TransportObserver($connections, $this->createPrintingUpdateHandler()),
            new TransportObserver($connections, $this->createDispatchingUpdateHandler())
        );
    }

    /**
     * @return PrintingUpdateHandler
     */
    protected function createPrintingUpdateHandler() {
        return new PrintingUpdateHandler(fopen('php://output', 'w'));
    }

    /**
     * @return DispatchingUpdateHandler
     */
    protected function createDispatchingUpdateHandler() {
        $parser = $this->getIrcParser();
        $eventFactory = new EventFactory();

        return new DispatchingUpdateHandler($this->eventDispatcher, $parser, $eventFactory);
    }

    /**
     * Prepares our EventDispatcher instance. All user supplied subscribers and listeners are registered. Additionally,
     * our internal subscribers are registered.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    protected function prepareEventDispatcher(EventDispatcherInterface $eventDispatcher) {
        $this->addSubscribersToEventDispatcher($this->config['core.subscribers'], $eventDispatcher);
        $this->addSubscribersToEventDispatcher($this->config['subscribers'], $eventDispatcher);
        $this->addListenersToEventDispatcher($this->config['listeners'], $eventDispatcher);

        foreach ($this->pluginManager->getPlugins() as $plugin) {
            $this->addListenersToEventDispatcher($plugin->getListeners(), $eventDispatcher);
        }
    }

    /**
     * Adds the supplied subscribers to the also supplied dispatcher instance.
     *
     * @param EventSubscriberInterface[] $subscribers
     * @param EventDispatcherInterface $eventDispatcher
     */
    protected function addSubscribersToEventDispatcher($subscribers, EventDispatcherInterface $eventDispatcher) {
        foreach ($subscribers as $subscriber) {
            $eventDispatcher->addSubscriber($subscriber);
        }
    }

    /**
     * Adds the supplied listeners to the also supplied dispatcher instance.
     *
     * @param array $listeners
     * @param EventDispatcherInterface $eventDispatcher
     */
    protected function addListenersToEventDispatcher($listeners, EventDispatcherInterface $eventDispatcher) {
        foreach ($listeners as $eventName => $callbacks) {
            foreach ($callbacks as $callback) {
                $eventDispatcher->addListener($eventName, $callback);
            }
        }
    }

    /**
     * Constructs a model representation of our connections, networks, etc, based on the data supplied in the
     * configuration data.
     *
     * @return \Phircy\Model\Connection[]
     */
    protected function createConnectionsFromConfig() {
        $networksConfig = $this->config['networks'];

        return $this->getConfigModelMapper()
            ->createConnections($networksConfig);
    }

    /**
     * Creates IrcTransport instances from the Connection models. These instances are then registered which each
     * instance, in the "transport" class property.
     *
     * @param IrcFactory $transportFactory
     * @param \Phircy\Model\Connection[] $connections
     * @return IrcTransport[]
     */
    protected function createTransportsFromConnections(IrcFactory $transportFactory, $connections) {
        $transports = array();

        foreach ($connections as $connection) {
            $transport = $this->createTransportFromNetwork($transportFactory, $connection->network);
            $connection->transport = $transport;
            array_push($transports, $transport);
        }

        return $transports;
    }

    /**
     * Creates an IrcTransport instance based on details contained within a Network instance.
     *
     * @param IrcFactory $transportFactory
     * @param Network $network
     * @return IrcTransport
     */
    protected function createTransportFromNetwork(IrcFactory $transportFactory, Network $network) {
        $server = $network->nextServer();

        return $transportFactory
            ->createConnection($server->host, $server->port, $server->ssl);
    }

    /**
     * @return \Phipe\Application
     */
    protected function getPhipe() {
        return $this->phipe;
    }

    /**
     * @param \Phipe\Application $phipe
     */
    public function setPhipe(\Phipe\Application $phipe) {
        $this->phipe = $phipe;
    }

    /**
     * @return ConfigModelMapper
     */
    protected function getConfigModelMapper() {
        return $this->configModelMapper;
    }

    /**
     * @param ConfigModelMapper $configModelMapper
     */
    public function setConfigModelMapper(ConfigModelMapper $configModelMapper) {
        $this->configModelMapper = $configModelMapper;
    }

    /**
     * @param array|ApplicationConfig|null $config
     */
    public function setConfig($config) {
        if (is_array($config)) {
            $config = new ApplicationConfig($config);
        }

        $this->config = $config;
    }

    /**
     * @return IrcParser
     */
    protected function getIrcParser() {
        return $this->config['irc.parser'];
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher() {
        return $this->config['core.event_dispatcher'];
    }

    /**
     * @return IrcFactory
     */
    protected function getIrcConnectionFactory() {
        return $this->config['irc.connection_factory'];
    }

    /**
     * @return PluginManager
     */
    protected function getPluginManager() {
        return $this->config['core.plugin_manager'];
    }
}