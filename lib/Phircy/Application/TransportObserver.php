<?php

namespace Phircy\Application;

use Phircy\Connection\IrcTransport;
use Phircy\Handler\UpdateHandler;
use Phircy\Model\Connection;

/**
 * The TransportObserver provides a convenient way to observe the IRC transport instances. When the transport layer
 * triggers updates, this observer performs all the relevant checks to ensure the update is relevant to us, and then
 * forwards the information onto the contained handler instance, which has a more convenient interface for handling
 * updates.
 *
 * The IRC transport instances can, of course, be observed with standard observers, but the intention here is to
 * simplify handling and reduce duplication of relevancy checks.
 *
 * @package Phircy\Application
 */
class TransportObserver implements \SplObserver
{
    /**
     * @var \SplObjectStorage|Connection[]
     */
    protected $connections;

    /**
     * Mapping of event -> internal handler method.
     *
     * @var array
     */
    protected static $eventMethodMap = array(
        IrcTransport::EVENT_CONNECT => 'pushConnectToHandler',
        IrcTransport::EVENT_DISCONNECT => 'pushDisconnectToHandler',
        IrcTransport::EVENT_CONNECT_FAIL => 'pushConnectFailedToHandler',
        IrcTransport::EVENT_READ => 'pushEventReadToHandler',
        IrcTransport::EVENT_WRITE => 'pushEventWriteToHandler',
    );

    /**
     * @param \SplObjectStorage|Connection[] $connections
     * @param UpdateHandler $handler
     */
    public function __construct(\SplObjectStorage $connections, UpdateHandler $handler)
    {
        $this->connections = $connections;
        $this->handler = $handler;
    }

    /**
     * Handle an update from a subject instance.
     *
     * @param \SplSubject $subject
     * @param mixed $event Optional event name supplied by Phipe
     * @param mixed $data Optional event data supplied by Phipe
     */
    public function update(\SplSubject $subject, $event = null, $data = null)
    {
        if ($subject instanceof IrcTransport) {
            // Update is relevant to us.
            $this->ircUpdate($subject, $event, $data);
        }
    }

    /**
     * Handle an IrcTransport update.
     *
     * @param IrcTransport $transport
     * @param mixed $event Event name supplied by Phipe
     * @param mixed $data Event data supplied by Phipe
     */
    protected function ircUpdate(IrcTransport $transport, $event, $data)
    {
        $connection = $this->resolveConnectionFromTransport($transport);

        $this->pushToHandler($connection, $event, $data);
    }

    /**
     * Given an IrcTransport instance, a Connection instance is returned.
     *
     * @param IrcTransport $transport
     * @throws \UnexpectedValueException Thrown if no relevant Connection can be found
     * @return Connection
     */
    protected function resolveConnectionFromTransport(IrcTransport $transport)
    {
        // Compare the supplied IrcTransport instance to all IrcTransport instances, until a match is found.
        foreach ($this->connections as $connection) {
            if ($connection->transport === $transport) {
                return $connection;
            }
        }

        throw new \UnexpectedValueException('No available connection for transport');
    }

    /**
     * Pushes the event information and a Connection instance into the relevant Handler method.
     *
     * @param \Phircy\Model\Connection $connection
     * @param mixed $event
     * @param mixed $data
     */
    protected function pushToHandler(Connection $connection, $event, $data)
    {
        // Events not in the map are simply not relevant; they can be silently ignored.
        if (array_key_exists($event, self::$eventMethodMap)) {
            $method = self::$eventMethodMap[$event];
            call_user_func(array($this, $method), $connection, $data);
        }
    }

    /**
     * @param Connection $connection
     */
    protected function pushConnectToHandler(Connection $connection)
    {
        $this->handler
            ->processConnect($this->connections, $connection);
    }

    /**
     * @param Connection $connection
     */
    protected function pushDisconnectToHandler(Connection $connection)
    {
        $this->handler
            ->processDisconnect($this->connections, $connection);
    }

    /**
     * @param Connection $connection
     */
    protected function pushConnectFailedToHandler(Connection $connection)
    {
        $this->handler
            ->processConnectFail($this->connections, $connection);
    }

    /**
     * @param Connection $connection
     */
    protected function pushEventReadToHandler(Connection $connection)
    {
        $this->handler
            ->processRead($this->connections, $connection, $connection->transport->readLines());
    }

    /**
     * @param Connection $connection
     * @param string $data
     */
    protected function pushEventWriteToHandler(Connection $connection, $data)
    {
        $lines = preg_split("#\r?\n#", $data, -1, PREG_SPLIT_NO_EMPTY);

        $this->handler
            ->processWrite($this->connections, $connection, $lines);
    }
}