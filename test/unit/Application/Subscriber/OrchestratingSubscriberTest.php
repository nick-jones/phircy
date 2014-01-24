<?php

namespace Phircy\Application\Subscriber;

require_once __DIR__ . '/SubscriberTestCase.php';

class OrchestratingSubscriberTest extends SubscriberTestCase {
    /**
     * @var OrchestratingSubscriber
     */
    protected $subscriber;

    /**
     * @var array
     */
    protected $config = array(
        'networks' => array(
            array(
                'nick' => 'mock_nick',
                'username' => 'mock_username',
                'realname' => 'mock_realname',
                'channels' => array('#mock')
            )
        )
    );

    protected function setUp() {
        $this->subscriber = new OrchestratingSubscriber($this->config);
    }

    public function testGetSubscribedEvents() {
        $this->assertInternalType('array', $this->subscriber->getSubscribedEvents());
    }

    public function testOnSocketConnect() {
        $transport = $this->getMock('\Phircy\Connection\IrcTransport', array('writeNick', 'writeUser'));

        $transport->expects($this->once())
            ->method('writeNick')
            ->with($this->equalTo('mock_nick'));

        $transport->expects($this->once())
            ->method('writeUser')
            ->with(
                $this->equalTo('mock_username'),
                $this->equalTo(0),
                $this->equalTo('*'),
                $this->equalTo('mock_realname')
            );

        $connection = new \Phircy\Model\Connection();
        $connection->transport = $transport;
        $connection->id = 0;

        $event = $this->createMockEvent(array(), $connection);

        $this->subscriber->onSocketConnect($event);
    }

    public function testOnSocketDisconnect() {
        $event = $this->createMockEvent();

        $dispatcher = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('irc.disconnect'), $this->equalTo($event));

        $this->subscriber->onSocketDisconnect($event, NULL, $dispatcher);
    }

    public function testOnSocketConnectFail() {
        $host = 'irc.mock.example';
        $port = 7000;
        $ssl = TRUE;

        $transport = $this->getMock('\Phircy\Connection\IrcTransport');

        $transport->expects($this->once())
            ->method('setHost')
            ->with($this->equalTo($host));

        $transport->expects($this->once())
            ->method('setPort')
            ->with($this->equalTo($port));

        $transport->expects($this->once())
            ->method('setSsl')
            ->with($this->equalTo($ssl));

        $network = $this->getMock('\Phircy\Model\Network');

        $network->expects($this->once())
            ->method('nextServer')
            ->will($this->returnValue(new \Phircy\Model\Server($host, $port, $ssl)));

        $connection = new \Phircy\Model\Connection();
        $connection->transport = $transport;
        $connection->network = $network;

        $event = $this->createMockEvent(array(), $connection);

        $this->subscriber->onSocketConnectFail($event);
    }

    public function testOnIrc001() {
        $event = $this->createMockEvent();

        $dispatcher = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('irc.connect'), $this->equalTo($event));

        $this->subscriber->onIrc001($event, NULL, $dispatcher);
    }

    public function testOnIrcConnect() {
        $server = 'irc.mock.example';

        $transport = $this->getMock('\Phircy\Connection\IrcTransport', array('writeJoin'));

        $transport->expects($this->once())
            ->method('writeJoin')
            ->with($this->equalTo('#mock'));

        $connection = new \Phircy\Model\Connection();
        $connection->transport = $transport;
        $connection->id = 0;

        $event = $this->createMockEvent(array($server), $connection);

        $this->subscriber->onIrcConnect($event);
    }

    public function testOnIrcPing() {
        $server = 'irc.mock.example';

        $transport = $this->getMock('\Phircy\Connection\IrcTransport', array('writePong'));

        $transport->expects($this->once())
            ->method('writePong')
            ->with($this->equalTo($server));

        $connection = new \Phircy\Model\Connection();
        $connection->transport = $transport;

        $event = $this->createMockEvent(array($server), $connection);

        $this->subscriber->onIrcPing($event);
    }
}