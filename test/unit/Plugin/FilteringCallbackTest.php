<?php

namespace Phircy\Plugin;

/**
 * @package Phircy\Plugin
 */
class FilteringCallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilteringCallback
     */
    protected $filteringCallback;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Phircy\Matcher\Matcher
     */
    protected $matcher;

    /**
     * @var int
     */
    protected $called;

    protected function setUp()
    {
        $this->called = 0;
        $this->matcher = $this->getMock('\Phircy\Matcher\Matcher');

        $this->filteringCallback = new FilteringCallback($this->matcher, function () {
            $this->called++;
        });
    }

    public function testInvokeHook()
    {
        $text = 'mock';

        $this->matcher->expects($this->once())
            ->method('matches')
            ->with($text)
            ->will($this->returnValue(true));

        $event = $this->createMockIrcEvent(array('text' => $text));

        $this->filteringCallback->__invoke($event);

        $this->assertEquals(1, $this->called);
    }

    public function testInvokeHookWithNonMatching()
    {
        $text = 'mock';

        $this->matcher->expects($this->once())
            ->method('matches')
            ->with($text)
            ->will($this->returnValue(false));

        $event = $this->createMockIrcEvent(array('text' => $text));

        $this->filteringCallback->__invoke($event);

        $this->assertEquals(0, $this->called);
    }

    /**
     * @param array $params
     * @return \PHPUnit_Framework_MockObject_MockObject|\Phircy\Event\IrcEvent
     */
    protected function createMockIrcEvent(array $params = array())
    {
        $event = $this->getMock('\Phircy\Event\IrcEvent');

        $event->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue($params));

        return $event;
    }
}