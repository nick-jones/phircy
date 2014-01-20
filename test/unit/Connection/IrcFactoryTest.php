<?php

namespace Phircy\Connection;

class IrcFactoryTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var IrcFactory
     */
    protected $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Phipe\Connection\Factory
     */
    protected $proxied;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Phergie\Irc\GeneratorInterface
     */
    protected $generator;

    protected function setUp() {
        $this->proxied = $this->getMock('\Phipe\Connection\Factory');
        $this->generator = $this->getMock('\Phergie\Irc\GeneratorInterface');

        $this->factory = new IrcFactory($this->proxied, $this->generator);
    }

    public function testCreateConnection() {
        $host = 'irc.mock.example';
        $port = 6667;
        $ssl = FALSE;

        $this->proxied->expects($this->once())
            ->method('createConnection')
            ->with($this->equalTo($host), $this->equalTo($port), $this->equalTo($ssl))
            ->will($this->returnValue($this->getMock('\Phipe\Connection\Connection')));

        $connection = $this->factory->createConnection($host, $port, $ssl);

        $this->assertInstanceOf('\Phircy\Connection\IrcTransport', $connection);
    }
}