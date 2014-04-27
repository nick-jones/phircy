<?php

namespace Phircy\Matcher;

/**
 * @package Phircy\Matcher
 */
class PatternMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PatternMatcher
     */
    protected $matcher;

    protected function setUp()
    {
        $this->matcher = new PatternMatcher('[^d]ock x*z[[:punct:]]');
    }

    public function testMatches()
    {
        $this->assertTrue($this->matcher->matches('mock xyz!'));
        $this->assertFalse($this->matcher->matches('dock xyz!'));
        $this->assertFalse($this->matcher->matches('mock xyz1'));
    }
}