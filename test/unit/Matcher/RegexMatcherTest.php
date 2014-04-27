<?php

namespace Phircy\Matcher;

class RegexMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegexMatcher
     */
    protected $matcher;

    protected function setUp()
    {
        $this->matcher = new RegexMatcher('/^mock [a-z]$/');
    }

    public function testMatches()
    {
        $this->assertTrue($this->matcher->matches('mock x'));
        $this->assertFalse($this->matcher->matches('mock !'));
        $this->assertFalse($this->matcher->matches(' mock x'));
    }
}