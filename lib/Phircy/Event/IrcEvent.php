<?php

namespace Phircy\Event;

/**
 * A representation of an event in the IRC world. Various methods provide information surrounding the event that has
 * occurred.
 *
 * @method \Phircy\Model\Connection getConnection()
 * @package Phircy\Event
 */
class IrcEvent extends \Symfony\Component\EventDispatcher\Event implements \Phergie\Irc\Event\EventInterface {
    /**
     * Phergie EvenTrait provides IRC event related accessors.
     */
    use \Phergie\Irc\Event\EventTrait;

    /**
     * @var \SplObjectStorage|\Phircy\Model\Connection[]
     */
    protected $connections;

    /**
     * @param \SplObjectStorage|\Phircy\Model\Connection[] $connections
     */
    public function setConnections(\SplObjectStorage $connections) {
        $this->connections = $connections;
    }

    /**
     * @return \SplObjectStorage|\Phircy\Model\Connection[]
     */
    public function getConnections() {
        return $this->connections;
    }
}