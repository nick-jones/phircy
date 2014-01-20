<?php

namespace Phircy\Model;

class ChannelTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Channel
     */
    protected $channel;

    protected function setUp() {
        $this->channel = new Channel('foo', array('i'));
    }

    public function testConstructor() {
        $this->assertEquals('foo', $this->channel->name);
        $this->assertEquals(array('i'), $this->channel->modes);
    }
}