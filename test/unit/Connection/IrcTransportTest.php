<?php

namespace Phircy\Connection;

class IrcTransportTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var IrcTransport
     */
    protected $transport;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Phipe\Connection\Connection
     */
    protected $proxied;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Phergie\Irc\GeneratorInterface
     */
    protected $generator;

    protected function setUp() {
        $this->proxied = $this->getMock('\Phipe\Connection\Connection');
        $this->generator = $this->getMock('\Phergie\Irc\GeneratorInterface');

        $this->transport = new IrcTransport($this->proxied, $this->generator);
    }

    public function testMagicCall() {
        $nick = 'Mock';
        $message = sprintf("NICK :%s\r\n", $nick);

        $this->generator->expects($this->once())
            ->method('ircNick')
            ->with($this->equalTo($nick))
            ->will($this->returnValue($message));

        $this->proxied->expects($this->once())
            ->method('write')
            ->with($this->equalTo($message));

        $this->transport->__call('writeNick', array($nick));
    }

    public function testMagicCall_NonWrite() {
        $this->setExpectedException('\BadMethodCallException', 'Undefined method');

        $this->transport->__call('foo', array());
    }

    public function testMagicCall_InvalidWrite() {
        $this->setExpectedException('\BadMethodCallException', 'Generator method "ircFoo" does not exist');

        $this->transport->__call('writeFoo', array());
    }

    public function testReadAll() {
        $this->transport->setReadBuffer("foo\r\nbar\nbaz\n");

        $result = $this->transport->readLines();

        $this->assertEquals(array('foo', 'bar', 'baz'), $result);
    }
}