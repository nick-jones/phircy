<?php

namespace Phircy\Application\Subscriber;

require_once __DIR__ . '/SubscriberTestCase.php';

class ModelUpdatingSubscriberTest extends SubscriberTestCase {
    /**
     * @var ModelUpdatingSubscriber
     */
    protected $subscriber;

    protected function setUp() {
        $this->subscriber = new ModelUpdatingSubscriber();
    }

    public function testGetSubscribedEvents() {
        $this->assertInternalType('array', $this->subscriber->getSubscribedEvents());
    }

    public function testOnSocketConnect() {
        $connection = $this->createMockConnection(FALSE);
        $event = $this->createMockEvent(array(), $connection);

        $this->subscriber->onSocketConnect($event);

        $this->assertTrue($connection->connected);
    }

    public function testOnSocketDisconnect() {
        $connection = $this->createMockConnection(TRUE);
        $event = $this->createMockEvent(array(), $connection);

        $this->subscriber->onSocketDisconnect($event);

        $this->assertFalse($connection->connected);
    }

    public function testOnIrcConnect() {
        $connection = $this->createMockConnection(FALSE);
        $event = $this->createMockEvent(array(), $connection);

        $this->subscriber->onIrcConnect($event);

        $this->assertTrue($connection->network->connected);
    }

    public function testOnIrcDisconnect() {
        $connection = $this->createMockConnection(TRUE);
        $event = $this->createMockEvent(array(), $connection);

        $this->subscriber->onIrcDisconnect($event);

        $this->assertFalse($connection->network->connected);
    }

    public function testOnIrcJoin() {
        $network = $this->getMock('\Phircy\Model\Network');

        $network->expects($this->once())
            ->method('addChannel')
            ->with($this->isInstanceOf('\Phircy\Model\Channel'));

        $connection = $this->createMockConnection(TRUE, $network);
        $event = $this->createMockTargetedEvent(array('channels' => '#mock'), $connection);

        $this->subscriber->onIrcJoin($event);
    }
    public function testOnIrcPart() {
        $channel = $this->getMock('\Phircy\Model\Channel', array(), array('#mock'));

        $network = $this->getMock('\Phircy\Model\Network');

        $network->expects($this->atLeastOnce())
            ->method('findChannelByName')
            ->with($this->equalTo('#mock'))
            ->will($this->returnValue($channel));

        $network->expects($this->once())
            ->method('removeChannel')
            ->with($this->equalTo($channel));

        $connection = $this->createMockConnection(TRUE, $network);
        $event = $this->createMockTargetedEvent(array('channels' => '#mock'), $connection);

        $this->subscriber->onIrcPart($event);
    }

    /**
     * @param bool $connected
     * @param \PHPUnit_Framework_MockObject_MockObject|\Phircy\Model\Network $network
     * @return \Phircy\Model\Connection
     */
    protected function createMockConnection($connected = TRUE, \Phircy\Model\Network $network = NULL) {
        if (!$network) {
            $network = new \Phircy\Model\Network();
        }

        $network->connected = $connected;

        $connection = new \Phircy\Model\Connection();
        $connection->connected = $connected;
        $connection->network = $network;

        return $connection;
    }
}