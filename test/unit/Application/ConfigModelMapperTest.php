<?php

namespace Phircy\Application;

class ConfigModelMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigModelMapper
     */
    protected $mapper;

    protected function setUp()
    {
        $this->mapper = new ConfigModelMapper();
    }

    public function testCreateConnections()
    {
        $host = 'irc.mock.example';
        $id = 7;

        $connections = $this->mapper->createConnections(
            array(
                $id => array(
                    'servers' => array('host' => $host),
                    'name' => 'mock'
                )
            )
        );

        $connections->rewind();
        /** @var \Phircy\Model\Connection $connection */
        $connection = $connections->current();

        $this->assertInstanceOf('\SplObjectStorage', $connections);
        $this->assertEquals(1, count($connections));
        $this->assertInstanceOf('\Phircy\Model\Connection', $connection);
        $this->assertEquals($host, $connection->network->servers[0]->host);
        $this->assertEquals($id, $connection->id);
    }

    public function testCreateNetwork()
    {
        $host = 'irc.mock.example';

        $network = $this->mapper->createNetwork(
            'mock',
            array(
                'host' => $host
            )
        );

        $this->assertInstanceOf('\Phircy\Model\Network', $network);
        $this->assertEquals($host, $network->servers[0]->host);
    }

    public function testCreateNetwork_Address()
    {
        $host = 'irc.mock.example';
        $port = 7000;

        $network = $this->mapper->createNetwork(
            'mock',
            array(
                sprintf('%s:+%d', $host, $port)
            )
        );

        $this->assertInstanceOf('\Phircy\Model\Network', $network);
        $this->assertEquals($host, $network->servers[0]->host);
        $this->assertEquals($port, $network->servers[0]->port);
        $this->assertTrue($network->servers[0]->ssl);
    }

    public function testCreateServer()
    {
        $host = 'irc.mock.example';
        $port = 7000;

        $server = $this->mapper->createServer(
            array(
                'host' => $host,
                'port' => $port,
                'ssl' => true
            )
        );

        $this->assertEquals($host, $server->host);
        $this->assertEquals($port, $server->port);
        $this->assertTrue($server->ssl);
    }
}