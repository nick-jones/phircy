<?php

namespace Phircy\Event;

class EventFactoryTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var EventFactory
     */
    protected $factory;

    protected function setUp() {
        $this->factory = new EventFactory();
    }

    public function testCreateFromParserOutput() {
        $command = 'NICK';
        $params = array('Mock');
        $message = 'mock';

        $output = array(
            'command' => $command,
            'params' => $params,
            'message' => $message
        );

        $event = $this->factory->createFromParserOutput($output);

        $this->assertEquals($command, $event->getCommand());
        $this->assertEquals($params, $event->getParams());
        $this->assertEquals($message, $event->getMessage());
    }

    public function testCreateFromParserOutput_Targeted() {
        $targets = array('Foo');

        $output = array(
            'targets' => $targets,
            'command' => 'mock',
            'params' => array('mock'),
            'message' => ''
        );

        $event = $this->factory->createFromParserOutput($output);

        $this->assertEquals($targets, $event->getTargets());
    }
}