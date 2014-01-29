<?php

namespace Phircy\Event;

class IrcEventTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var IrcEvent
     */
    protected $event;

    protected function setUp() {
        $this->event = new IrcEvent();
    }

    public function testSetConnections() {
        $connections = new \SplObjectStorage();
        $this->event->setConnections($connections);

        $this->assertSame($connections, $this->event->getConnections());
    }

    public function testGetConnections() {
        $this->assertEquals(NULL, $this->event->getConnections());
    }

    public function testGetTransport() {
        $transport = $this->getMock('\Phircy\Connection\IrcTransport');

        $connection = new \Phircy\Model\Connection();
        $connection->transport = $transport;

        $this->event->setConnection($connection);

        $this->assertEquals($transport, $this->event->getTransport());
    }
}