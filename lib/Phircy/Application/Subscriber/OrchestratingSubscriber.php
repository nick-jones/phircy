<?php

namespace Phircy\Application\Subscriber;

use Phircy\Event\IrcEvent;
use Phircy\Event\Priorities;
use Phircy\Model\Connection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * The job of the OrchestratingSubscriber is to ensure all IRC movement (connections, disconnections, etc,) are handled
 * correctly, and that we stay connected (by responding to PING requests.)
 *
 * @package Phircy\Application\Subscriber
 */
class OrchestratingSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface {
    /**
     * @var array|\Phircy\ApplicationConfig
     */
    protected static $config;

    /**
     * @param array|\Phircy\ApplicationConfig $config
     */
    public function __construct($config) {
        self::$config = $config;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents() {
        return array(
            'socket.connect' => array('onSocketConnect', Priorities::PHIRCY_STANDARD),
            'socket.disconnect' => array('onSocketDisconnect', Priorities::PHIRCY_PRE),
            'irc.001' => array('onIrc001', Priorities::PHIRCY_STANDARD),
            'irc.connect' => array('onIrcConnect', Priorities::PHIRCY_STANDARD),
            'irc.ping' => array('onIrcPing', Priorities::PHIRCY_STANDARD)
        );
    }

    /**
     * Send NICK and USER once the transport mechanism has successfully connected.
     *
     * @param IrcEvent $event
     */
    public static function onSocketConnect(IrcEvent $event) {
        $connection = $event->getConnection();
        $details = self::networkDetailsFromConnection($connection);
        $transport = $connection->transport;

        $transport->writeNick($details['nick']);
        $transport->writeUser($details['username'], '0', '*', $details['realname']);
    }

    /**
     * Provide a more user friendly event once we have successfully connected.
     *
     * @param IrcEvent $event
     * @param string $eventName
     * @param EventDispatcherInterface $dispatcher
     */
    public static function onIrc001(IrcEvent $event, $eventName, EventDispatcherInterface $dispatcher) {
        $dispatcher->dispatch('irc.connect', $event);
    }

    /**
     * Provide an IRC related event on disconnect.
     *
     * @param IrcEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     */
    public static function onSocketDisconnect(IrcEvent $event, $eventName, EventDispatcherInterface $dispatcher) {
        $dispatcher->dispatch('irc.disconnect', $event);
    }

    /**
     * Join any channels listed in config once connected to IRC.
     *
     * @param IrcEvent $event
     */
    public static function onIrcConnect(IrcEvent $event) {
        $connection = $event->getConnection();
        $details = self::networkDetailsFromConnection($connection);
        $channels = isset($details['channels']) ? $details['channels'] : array();

        foreach ($channels as $channel) {
            $connection->transport
                ->writeJoin($channel);
        }
    }

    /**
     * Respond to PING requests from the IRC server.
     *
     * @param IrcEvent $event
     */
    public static function onIrcPing(IrcEvent $event) {
        $server = array_shift($event->getParams());

        $event->getConnection()
            ->transport
            ->writePong($server);
    }

    /**
     * Convenience method to fetch config details for a given Connection.
     *
     * @param Connection $connection
     * @return array
     */
    protected static function networkDetailsFromConnection(Connection $connection) {
        return self::$config['networks'][$connection->id];
    }
}