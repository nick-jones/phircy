<?php

namespace Phircy;

class ApplicationConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApplicationConfig
     */
    protected $container;

    protected function setUp()
    {
        $this->container = new ApplicationConfig();
    }

    public function testCreateDefaultValues()
    {
        $this->assertEquals(array(), $this->container['networks']);
        $this->assertEquals(array(), $this->container['listeners']);
        $this->assertEquals(array(), $this->container['subscribers']);
    }

    public function testCreateDefaultFactories()
    {
        $this->assertEquals('Phircy\Connection\IrcFactory', get_class($this->container['irc.connection_factory']));
        $this->assertEquals('Phergie\Irc\Generator', get_class($this->container['irc.generator']));
        $this->assertEquals('Phircy\Parser\PhergieIrcParser', get_class($this->container['irc.parser']));
        $this->assertEquals(
            'Symfony\Component\EventDispatcher\EventDispatcher',
            get_class($this->container['core.event_dispatcher'])
        );
        $this->assertInternalType('array', $this->container['core.subscribers']);
        $this->assertEquals(
            'Phipe\Connection\Stream\StreamFactory',
            get_class($this->container['core.transport_factory'])
        );
    }
}