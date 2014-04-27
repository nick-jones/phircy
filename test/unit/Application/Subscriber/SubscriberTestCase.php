<?php

namespace Phircy\Application\Subscriber;

class SubscriberTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $params
     * @param \Phircy\Model\Connection $connection
     * @return \PHPUnit_Framework_MockObject_MockObject|\Phircy\Event\IrcEvent
     */
    protected function createMockEvent(array $params = array(), \Phircy\Model\Connection $connection = null)
    {
        $event = $this->getMock('\Phircy\Event\IrcEvent');

        $this->attachParamsToMockEvent($event, $params);
        $this->attachConnectionToMockEvent($event, $connection);

        return $event;
    }

    /**
     * @param array $params
     * @param \Phircy\Model\Connection $connection
     * @return \PHPUnit_Framework_MockObject_MockObject|\Phircy\Event\IrcEvent
     */
    protected function createMockTargetedEvent(array $params = array(), \Phircy\Model\Connection $connection = null)
    {
        $event = $this->getMock('\Phircy\Event\TargetedIrcEvent');

        $this->attachParamsToMockEvent($event, $params);
        $this->attachConnectionToMockEvent($event, $connection);

        return $event;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $event
     * @param array $params
     */
    protected function attachParamsToMockEvent(\PHPUnit_Framework_MockObject_MockObject $event, array $params = array())
    {
        $event->expects($this->any())
            ->method('getParams')
            ->will($this->returnValue($params));
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $event
     * @param \Phircy\Model\Connection $connection
     */
    protected function attachConnectionToMockEvent(
        \PHPUnit_Framework_MockObject_MockObject $event,
        \Phircy\Model\Connection $connection = null
    ) {
        $event->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connection));
    }
}