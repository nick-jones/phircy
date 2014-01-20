<?php

namespace Phircy\Model;

class NetworkTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Network
     */
    protected $network;

    protected function setUp() {
        $this->network = new Network(array('foo'), 'bar');
    }

    public function testConstructor() {
        $this->assertEquals(array('foo'), $this->network->servers);
        $this->assertEquals('bar', $this->network->name);
    }

    public function testAddChannel() {
        $channel = new \Phircy\Model\Channel('foo');

        $this->network->addChannel($channel);

        $this->assertArrayHasKey('foo', $this->network->channels);
        $this->assertEquals($channel, $this->network->channels['foo']);
    }

    public function testRemoveChannel() {
        $channel = new \Phircy\Model\Channel('foo');

        $this->network->addChannel($channel);
        $this->network->removeChannel($channel);

        $this->assertEquals(0, count($this->network->channels));
    }

    public function testFindChannelByName() {
        $channel = new \Phircy\Model\Channel('foo');

        $this->network->addChannel($channel);
        $result = $this->network->findChannelByName('foo');

        $this->assertEquals($channel, $result);
    }

    public function testFindChannelByName_Missing() {
        $result = $this->network->findChannelByName('foo');

        $this->assertNull($result);
    }
}