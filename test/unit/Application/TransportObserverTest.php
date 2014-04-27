<?php

namespace Phircy\Application;

use Phircy\Connection\IrcTransport;

class TransportObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TransportObserver
     */
    protected $observer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Phircy\Connection\IrcTransport
     */
    protected $transport;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Phircy\Model\Connection
     */
    protected $connection;

    /**
     * @var \SplObjectStorage
     */
    protected $connections;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Phircy\Handler\UpdateHandler
     */
    protected $handler;

    protected function setUp()
    {
        $this->transport = $this->getMock('\Phircy\Connection\IrcTransport');

        $this->connection = new \Phircy\Model\Connection();
        $this->connection->transport = $this->transport;

        $this->connections = new \SplObjectStorage();
        $this->connections->attach($this->connection);

        $this->handler = $this->getMock('\Phircy\Handler\UpdateHandler');

        $this->observer = new TransportObserver($this->connections, $this->handler);
    }

    public function testUpdateWithIrrelevantSubject()
    {
        $subject = $this->getMock('\SplSubject');

        $this->observer->update($subject);
    }

    public function testUpdateWithConnect()
    {
        $this->handler->expects($this->once())
            ->method('processConnect')
            ->with($this->equalTo($this->connections), $this->equalTo($this->connection));

        $this->observer->update($this->transport, IrcTransport::EVENT_CONNECT);
    }

    public function testUpdateWithDisconnect()
    {
        $this->handler->expects($this->once())
            ->method('processDisconnect')
            ->with($this->equalTo($this->connections), $this->equalTo($this->connection));

        $this->observer->update($this->transport, IrcTransport::EVENT_DISCONNECT);
    }

    public function testUpdateWithConnectFail()
    {
        $this->handler->expects($this->once())
            ->method('processConnectFail')
            ->with($this->equalTo($this->connections), $this->equalTo($this->connection));

        $this->observer->update($this->transport, IrcTransport::EVENT_CONNECT_FAIL);
    }

    public function testUpdateWithRead()
    {
        $data = array('mock');

        $this->transport->expects($this->once())
            ->method('readLines')
            ->will($this->returnValue($data));

        $this->handler->expects($this->once())
            ->method('processRead')
            ->with($this->equalTo($this->connections), $this->equalTo($this->connection), $this->equalTo($data));

        $this->observer->update($this->transport, IrcTransport::EVENT_READ);
    }

    public function testUpdateWithWrite()
    {
        $data = array('mock', 'foo');

        $this->handler->expects($this->once())
            ->method('processWrite')
            ->with($this->equalTo($this->connections), $this->equalTo($this->connection), $this->equalTo($data));

        $this->observer->update($this->transport, IrcTransport::EVENT_WRITE, implode(PHP_EOL, $data));
    }

    public function testUpdateWithEOF()
    {
        $this->observer->update($this->transport, IrcTransport::EVENT_EOF);
    }

    public function testUpdateWithMissingConnection()
    {
        $this->setExpectedException('UnexpectedValueException', 'No available connection for transport');

        $this->connections->detach($this->connection);

        $this->observer->update($this->transport);
    }
}