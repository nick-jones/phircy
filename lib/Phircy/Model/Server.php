<?php

namespace Phircy\Model;

/**
 * @package Phircy\Model
 */
class Server {
    /**
     * @var string
     */
    public $host;

    /**
     * @var int
     */
    public $port;

    /**
     * @var bool
     */
    public $ssl;

    /**
     * @var
     */
    public $name;

    /**
     * @param string $host
     * @param int $port
     * @param bool $ssl
     */
    public function __construct($host, $port = 6667, $ssl = FALSE) {
        $this->host = $host;
        $this->port = $port;
        $this->ssl = $ssl;
    }
}