<?php

namespace Phircy\Model;

/**
 * @package Phircy\Model
 */
class Network {
    /**
     * @var string
     */
    public $name;

    /**
     * @var Server[]
     */
    public $servers = array();

    /**
     * @var Channel[]
     */
    public $channels = array();

    /**
     * @var User[]
     */
    public $users = array();

    /**
     * @var bool
     */
    public $connected = FALSE;

    /**
     * @param Server[] $servers
     * @param string $name
     */
    public function __construct(array $servers = array(), $name = NULL) {
        $this->name = $name;
        $this->servers = $servers;
    }

    /**
     * @param Channel $channel
     */
    public function addChannel(Channel $channel) {
        $this->channels[$channel->name] = $channel;
    }

    /**
     * @param Channel $channel
     */
    public function removeChannel(Channel $channel) {
        unset($this->channels[$channel->name]);
    }

    /**
     * @param string $name
     * @return Channel
     */
    public function findChannelByName($name) {
        return $this->channels[$name];
    }
}