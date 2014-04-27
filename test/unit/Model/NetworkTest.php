<?php

namespace Phircy\Model;

class NetworkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Network
     */
    protected $network;

    protected function setUp()
    {
        $this->network = new Network(array('foo'), 'bar');
    }

    public function testConstructor()
    {
        $this->assertEquals(array('foo'), $this->network->servers);
        $this->assertEquals('bar', $this->network->name);
    }

    public function testAddChannel()
    {
        $channel = new \Phircy\Model\Channel('foo');

        $this->network->addChannel($channel);

        $this->assertArrayHasKey('foo', $this->network->channels);
        $this->assertEquals($channel, $this->network->channels['foo']);
    }

    public function testRemoveChannel()
    {
        $channel = new \Phircy\Model\Channel('foo');

        $this->network->addChannel($channel);
        $this->network->removeChannel($channel);

        $this->assertEquals(0, count($this->network->channels));
    }

    public function testFindChannelByName()
    {
        $channel = new \Phircy\Model\Channel('foo');

        $this->network->addChannel($channel);
        $result = $this->network->findChannelByName('foo');

        $this->assertEquals($channel, $result);
    }

    public function testFindChannelByName_Missing()
    {
        $result = $this->network->findChannelByName('foo');

        $this->assertNull($result);
    }

    public function testNextServer()
    {
        $server1 = $this->getMock('\Phircy\Model\Server', array(), array('127.0.0.1'));
        $server2 = $this->getMock('\Phircy\Model\Server', array(), array('127.0.0.2'));

        $this->network->servers = array($server1, $server2);

        $this->assertEquals($server1, $this->network->nextServer());
        $this->assertEquals($server2, $this->network->nextServer());
        $this->assertEquals($server1, $this->network->nextServer());
    }
}