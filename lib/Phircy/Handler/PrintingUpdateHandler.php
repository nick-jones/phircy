<?php

namespace Phircy\Handler;

use Phircy\Model\Connection;

class PrintingUpdateHandler implements UpdateHandler {
    /**
     * @var resource
     */
    protected $handle;

    /**
     * @param resource $handle
     */
    public function __construct($handle) {
        $this->handle = $handle;
    }

    /**
     * @param \SplObjectStorage|Connection[] $connections
     * @param Connection $connection
     */
    public function processConnect(\SplObjectStorage $connections, Connection $connection) {
        $this->printMessage($connection, 'Connected!');
    }

    /**
     * @param \SplObjectStorage|Connection[] $connections
     * @param Connection $connection
     */
    public function processDisconnect(\SplObjectStorage $connections, Connection $connection) {
        $this->printMessage($connection, 'Disconnected!');
    }

    /**
     * @param \SplObjectStorage|Connection[] $connections
     * @param Connection $connection
     * @param array $lines
     */
    public function processRead(\SplObjectStorage $connections, Connection $connection, array $lines) {
        foreach ($lines as $line) {
            $this->printMessage($connection, sprintf('← %s', $line));
        }
    }

    /**
     * @param \SplObjectStorage|Connection[] $connections
     * @param Connection $connection
     * @param array $lines
     */
    public function processWrite(\SplObjectStorage $connections, Connection $connection, array $lines) {
        foreach ($lines as $line) {
            $this->printMessage($connection, sprintf('→ %s', $line));
        }
    }

    /**
     * @param Connection $connection
     * @param string $message
     */
    protected function printMessage(Connection $connection, $message) {
        $output = sprintf("[%s] (%s) %s\n", gmdate('Y-m-d H:i:s'), $connection->network->name, $message);
        fwrite($this->handle, $output);
    }
}