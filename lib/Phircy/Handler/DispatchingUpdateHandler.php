<?php

namespace Phircy\Handler;

use Phircy\Event\EventFactory;
use Phircy\Event\IrcEvent;
use Phircy\Model\Connection;
use Phircy\Parser\IrcParser;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This UpdateHandler implementation parses IRC activity and dispatches IrcEvent instances based on the results.
 *
 * @package Phircy\Handler
 */
class DispatchingUpdateHandler implements UpdateHandler {
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Phircy\Parser\IrcParser
     */
    protected $parser;

    /**
     * @var \Phircy\Event\EventFactory
     */
    protected $eventFactory;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param IrcParser $parser
     * @param EventFactory $eventFactory
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, IrcParser $parser, EventFactory $eventFactory) {
        $this->eventDispatcher = $eventDispatcher;
        $this->parser = $parser;
        $this->eventFactory = $eventFactory;
    }

    /**
     * @param \SplObjectStorage|Connection[] $connections
     * @param Connection $connection
     */
    public function processConnect(\SplObjectStorage $connections, Connection $connection) {
        $event = new IrcEvent();
        $event->setConnection($connection);
        $event->setConnections($connections);

        $this->eventDispatcher
            ->dispatch('socket.connect', $event);
    }

    /**
     * @param \SplObjectStorage|Connection[] $connections
     * @param Connection $connection
     */
    public function processDisconnect(\SplObjectStorage $connections, Connection $connection) {
        $event = new IrcEvent();
        $event->setConnection($connection);
        $event->setConnections($connections);

        $this->eventDispatcher
            ->dispatch('socket.disconnect', $event);
    }

    /**
     * @param \SplObjectStorage|Connection[] $connections
     * @param Connection $connection
     * @param array $lines
     */
    public function processRead(\SplObjectStorage $connections, Connection $connection, array $lines) {
        foreach ($lines as $line) {
            $this->processIrcLine($connections, $connection, $line, 'irc');
        }
    }

    /**
     * @param \SplObjectStorage|Connection[] $connections
     * @param Connection $connection
     * @param array $lines
     */
    public function processWrite(\SplObjectStorage $connections, Connection $connection, array $lines) {
        foreach ($lines as $line) {
            $this->processIrcLine($connections, $connection, $line, 'irc.write');
        }
    }

    /**
     * Takes an IRC line and triggers an event based on its contents.
     *
     * @param \SplObjectStorage|Connection[] $connections
     * @param Connection $connection
     * @param $line
     * @param $eventPrefix
     */
    protected function processIrcLine(\SplObjectStorage $connections, Connection $connection, $line, $eventPrefix) {
        $event = $this->eventFromIrcLine($line);
        $event->setConnection($connection);
        $event->setConnections($connections);

        $eventName = sprintf('%s.%s', $eventPrefix, strtolower($event->getCommand()));

        $this->eventDispatcher
            ->dispatch($eventName, $event);
    }

    /**
     * Creates an event from an IRC line.
     *
     * @param string $line
     * @return IrcEvent
     */
    protected function eventFromIrcLine($line) {
        $parserOutput = $this->parser->parse($line);

        return $this->eventFactory
            ->createFromParserOutput($parserOutput);
    }
}