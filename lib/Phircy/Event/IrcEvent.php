<?php

namespace Phircy\Event;

use Phergie\Irc\Event\EventInterface;
use Phergie\Irc\Event\EventTrait;
use Symfony\Component\EventDispatcher\Event;

/**
 * A representation of an event in the IRC world. Various methods provide information surrounding the event that has
 * occurred.
 *
 * @method \Phircy\Model\Connection getConnection()
 * @package Phircy\Event
 */
class IrcEvent extends Event implements EventInterface
{
    /**
     * Phergie EvenTrait provides IRC event related accessors.
     */
    use EventTrait;

    /**
     * @var \SplObjectStorage|\Phircy\Model\Connection[]
     */
    protected $connections;

    /**
     * @param \SplObjectStorage|\Phircy\Model\Connection[] $connections
     */
    public function setConnections(\SplObjectStorage $connections)
    {
        $this->connections = $connections;
    }

    /**
     * @return \SplObjectStorage|\Phircy\Model\Connection[]
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Shortcut to retrieve the connections transport instance, as this is a common requirement when dealing
     * with event instances.
     *
     * @return \Phircy\Connection\IrcTransport
     */
    public function getTransport()
    {
        return $this->getConnection()
            ->transport;
    }
}