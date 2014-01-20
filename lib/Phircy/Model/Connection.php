<?php

namespace Phircy\Model;

/**
 * @package Phircy\Model
 */
class Connection {
    /**
     * @var int
     */
    public $id;

    /**
     * @var bool
     */
    public $connected = FALSE;

    /**
     * @var \Phircy\Connection\IrcTransport
     */
    public $transport;

    /**
     * @var User
     */
    public $me;

    /**
     * @var Network
     */
    public $network;

    /**
     * @var Server
     */
    public $server;
}