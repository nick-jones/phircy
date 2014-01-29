<?php

namespace Phircy\Matcher;

class IrcEventTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var CommandMatcher
     */
    protected $matcher;

    protected function setUp() {
        $this->matcher = new CommandMatcher('!mock');
    }

    public function testMatches() {
        $this->assertTrue($this->matcher->matches('!mock'));
        $this->assertTrue($this->matcher->matches('!mock this'));

        $this->assertFalse($this->matcher->matches('mock'));
        $this->assertFalse($this->matcher->matches('!rock'));
        $this->assertFalse($this->matcher->matches('!mocks'));
    }
}