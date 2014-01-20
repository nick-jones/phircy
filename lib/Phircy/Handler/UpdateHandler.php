<?php

namespace Phircy\Handler;

use Phircy\Model\Connection;

/**
 * UpdateHandler implementations should be used with the TransportObserver class for consuming IRC activity.
 *
 * @package Phircy\Handler
 */
interface UpdateHandler {
    /**
     * @param \SplObjectStorage|Connection[] $connections
     * @param Connection $connection
     */
    public function processConnect(\SplObjectStorage $connections, Connection $connection);

    /**
     * @param \SplObjectStorage|Connection[] $connections
     * @param Connection $connection
     * @return
     */
    public function processDisconnect(\SplObjectStorage $connections, Connection $connection);

    /**
     * @param \SplObjectStorage|Connection[] $connections
     * @param Connection $connection
     * @param array $lines
     */
    public function processRead(\SplObjectStorage $connections, Connection $connection, array $lines);

    /**
     * @param \SplObjectStorage|Connection[] $connections
     * @param Connection $connection
     * @param array $lines
     */
    public function processWrite(\SplObjectStorage $connections, Connection $connection, array $lines);
}