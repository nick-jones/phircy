<?php

namespace Phircy\Application;

use Phircy\Model\Connection;
use Phircy\Model\Network;
use Phircy\Model\Server;

/**
 * This class provides methods to translate user config into relevant model methods, for use internally. This is
 * required, as user config is supplied in as an array. The methods simply translate the data contained within the
 * array to form our model representation.
 *
 * @package Phircy\Application
 */
class ConfigModelMapper {
    /**
     * Create a collection of Connections based on the supplied configuration data.
     *
     * @param array $config
     * @return \SplObjectStorage|Connection[]
     */
    public function createConnections(array $config) {
        $connections = new \SplObjectStorage();

        foreach ($config as $id => $details) {
            $connection = new Connection();
            $connection->id = $id;
            $connection->network = $this->createNetwork($details['name'], (array) $details['servers']);

            $connections->attach($connection);
        }

        return $connections;
    }

    /**
     * Create a Network instance based on the supplied configuration data.
     *
     * @param string $name
     * @param array $config
     * @return Network
     */
    public function createNetwork($name, array $config) {
        $servers = $config ? $this->createServers($config) : array();

        return new Network($servers, $name);
    }

    /**
     * Create a collection of Server instances based on the supplied configuration data.
     *
     * @param array $config
     * @return array
     */
    public function createServers(array $config) {
        $servers = array();

        foreach ($config as $details) {
            if (is_string($details)) {
                $details = $this->parseAddress($details);
            }

            array_push($servers, $this->createServer($details));
        }

        return $servers;
    }

    /**
     * Create a single server instance based on the supplied configuration data.
     *
     * @param array $config
     * @return Server
     */
    public function createServer(array $config) {
        $host = isset($config['host']) ? $config['host'] : NULL;
        $port = isset($config['port']) ? $config['port'] : 6667;
        $ssl = isset($config['ssl']) ? $config['ssl'] : FALSE;

        return new Server($host, $port, $ssl);
    }

    /**
     * Helper method to parse IRC address. Note that parse_url has not been used here, as it does not handle some
     * IRC address conventions (e.g. the + port prefix to indicate SSL is required.)
     *
     * @param string $address The host address, e.g. (irc.dal.net:+7000)
     * @return array Address details. Keys are: "host", "port", "ssl".
     */
    protected function parseAddress($address) {
        preg_match('#^(?:irc://)?(.*?)(?::(\+?)([0-9]+))?$#', $address, $matches);

        return array(
            'host' => $matches[1],
            'port' => isset($matches[3]) ? $matches[3] : NULL,
            'ssl' => isset($matches[2]) && $matches[2] === '+'
        );
    }
}