<?php

namespace Phircy\Handler;

class DispatchingUpdateHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DispatchingUpdateHandler
     */
    protected $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDispatcher;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Phircy\Parser\IrcParser
     */
    protected $parser;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Phircy\Event\EventFactory
     */
    protected $eventFactory;

    protected function setUp()
    {
        $this->eventDispatcher = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->parser = $this->getMock('\Phircy\Parser\IrcParser');
        $this->eventFactory = $this->getMock('\Phircy\Event\EventFactory');

        $this->handler = new DispatchingUpdateHandler($this->eventDispatcher, $this->parser, $this->eventFactory);
    }

    public function testProcessConnect()
    {
        $connections = new \SplObjectStorage();
        $connection = $this->getMock('\Phircy\Model\Connection');

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('socket.connect'), $this->isInstanceOf('\Phircy\Event\IrcEvent'));

        $this->handler->processConnect($connections, $connection);
    }

    public function testProcessDisconnect()
    {
        $connections = new \SplObjectStorage();
        $connection = $this->getMock('\Phircy\Model\Connection');

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('socket.disconnect'), $this->isInstanceOf('\Phircy\Event\IrcEvent'));

        $this->handler->processDisconnect($connections, $connection);
    }

    public function testProcessConnectFailed()
    {
        $connections = new \SplObjectStorage();
        $connection = $this->getMock('\Phircy\Model\Connection');

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('socket.connect_fail'), $this->isInstanceOf('\Phircy\Event\IrcEvent'));

        $this->handler->processConnectFail($connections, $connection);
    }

    public function testProcessRead()
    {
        $connections = new \SplObjectStorage();
        $connection = $this->getMock('\Phircy\Model\Connection');
        $command = 'NICK';
        $nick = 'Mock';
        $message = sprintf('%s :%s', $command, $nick);
        $output = array('command' => $command, 'params' => array($nick));

        $event = $this->getMock('\Phircy\Event\IrcEvent');

        $event->expects($this->once())
            ->method('getCommand')
            ->will($this->returnValue($command));

        $this->parser->expects($this->once())
            ->method('parse')
            ->with($this->equalTo($message))
            ->will($this->returnValue($output));

        $this->eventFactory->expects($this->once())
            ->method('createFromParserOutput')
            ->with($this->equalTo($output))
            ->will($this->returnValue($event));

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('irc.nick'), $this->equalTo($event));

        $this->handler->processRead($connections, $connection, array($message));
    }

    public function testProcessWrite()
    {
        $connections = new \SplObjectStorage();
        $connection = $this->getMock('\Phircy\Model\Connection');
        $command = 'PART';
        $channel = '#Mock';
        $message = sprintf('%s :%s', $command, $channel);
        $output = array('command' => $command, 'params' => array($channel));

        $event = $this->getMock('\Phircy\Event\IrcEvent');

        $event->expects($this->once())
            ->method('getCommand')
            ->will($this->returnValue($command));

        $this->parser->expects($this->once())
            ->method('parse')
            ->will($this->returnValue($output));

        $this->eventFactory->expects($this->once())
            ->method('createFromParserOutput')
            ->will($this->returnValue($event));

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('irc.write.part'), $this->equalTo($event));

        $this->handler->processWrite($connections, $connection, array($message));
    }
}