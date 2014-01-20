<?php

namespace Phircy\Application\Subscriber;

use Phircy\Event\IrcEvent;
use Phircy\Event\Priorities;
use Phircy\Event\TargetedIrcEvent;
use Phircy\Model\Channel;

/**
 * This subscriber should ensure the IRC model components are updated by relevant IRC events (e.g. JOINs, PARTs, etc.).
 *
 * Some parts are currently not functional, as the phergie-irc-event package does not provide a means to retrieve the
 * source of IRC events; see https://github.com/phergie/phergie-irc-event/issues/11
 *
 * @package Phircy\Application\Subscriber
 */
class ModelUpdatingSubscriber implements \Symfony\Component\EventDispatcher\EventSubscriberInterface {
    /**
     * @return array
     */
    public static function getSubscribedEvents() {
        return array(
            'socket.connect' => array('onSocketConnect', Priorities::PHIRCY_STANDARD),
            'socket.disconnect' => array('onSocketDisconnect', Priorities::PHIRCY_STANDARD),
            'irc.connect' => array('onIrcDisconnect', Priorities::PHIRCY_STANDARD),
            'irc.disconnect' => array('onIrcDisconnect', Priorities::PHIRCY_STANDARD),
            //'irc.join' => array('onIrcJoin', Priorities::PHIRCY_STANDARD),
            //'irc.part' => array('onIrcPart', Priorities::PHIRCY_STANDARD)
        );
    }

    /**
     * When the socket connects, we must mark the Connection instance as connected.
     *
     * @param IrcEvent $event
     */
    public static function onSocketConnect(IrcEvent $event) {
        $event->getConnection()
            ->connected = TRUE;
    }

    /**
     * When the socket disconnects, we must mark the Connection instance as disconnected.
     *
     * @param IrcEvent $event
     */
    public static function onSocketDisconnect(IrcEvent $event) {
        $event->getConnection()
            ->connected = FALSE;
    }

    /**
     * When we connect to IRC, we must mark the Network instance as connected.
     *
     * @param IrcEvent $event
     */
    public static function onIrcConnect(IrcEvent $event) {
        $event->getConnection()
            ->network
            ->connected = TRUE;
    }

    /**
     * When we disconnect from IRC, we must mark the Network instance as disconnected.
     *
     * @param IrcEvent $event
     */
    public static function onIrcDisconnect(IrcEvent $event) {
        $event->getConnection()
            ->network
            ->connected = FALSE;
    }

    /**
     * When we join a channel, we must add that Channel to the collection of active channels for that network.
     *
     * @param IrcEvent $event
     */
    public static function onIrcJoin(IrcEvent $event) {
        $params = $event->getParams();
        $name = $params['channels'];

        $event->getConnection()
            ->network
            ->addChannel(new Channel($name));
    }

    /**
     * When we part a channel, we must remove that channel from the collection of active channels for that network.
     *
     * @param TargetedIrcEvent $event
     */
    public static function onIrcPart(TargetedIrcEvent $event) {
        $params = $event->getParams();
        $name = $params['channels'];

        $network = $event->getConnection()
            ->network;

        $network->removeChannel(
            $network->findChannelByName($name)
        );
    }
}