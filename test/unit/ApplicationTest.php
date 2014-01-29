<?php

namespace Phircy;

/**
 * @package Phipe
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Application
     */
    protected $application;

    protected function setUp() {
        $this->application = new Application();
    }

    public function testExecute() {
        $host = 'irc.mock.example';

        $networks = array(
            'servers' => array(
                'host' => $host
            )
        );

        $phipe = $this->getMock('\Phipe\Application');

        $phipe->expects($this->once())
            ->method('setConfig')
            ->with($this->isType('array'));

        $phipe->expects($this->once())
            ->method('execute');

        $connections = new \SplObjectStorage();

        $connections->attach($this->createConnection($host));

        $configModelMapper = $this->getMock('\Phircy\Application\ConfigModelMapper');

        $configModelMapper->expects($this->atLeastOnce())
            ->method('createConnections')
            ->with($this->equalTo($networks))
            ->will($this->returnValue($connections));

        $subscriber = $this->getMock('\Symfony\Component\EventDispatcher\EventSubscriberInterface');
        $coreSubscriber = $this->getMock('\Symfony\Component\EventDispatcher\EventSubscriberInterface');
        $listener = function() {};

        $plugin = $this->getMock('\Phircy\Plugin\Plugin');

        $plugin->expects($this->any())
            ->method('getListeners')
            ->will($this->returnValue(array('irc.privmsg' => array($listener))));

        $eventDispatcher = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $eventDispatcher->expects($this->exactly(2))
            ->method('dispatch')
            ->with($this->stringStartsWith('core.'));

        $eventDispatcher->expects($this->exactly(2))
            ->method('addSubscriber');

        $eventDispatcher->expects($this->exactly(2))
            ->method('addListener');

        $ircFactory = $this->getMock('\Phircy\Connection\IrcFactory', array(), array(
            $this->getMock('\Phipe\Connection\Factory'),
            $this->getMock('\Phergie\Irc\GeneratorInterface')
        ));

        $ircFactory->expects($this->once())
            ->method('createConnection')
            ->with($this->equalTo($host), $this->equalTo(6667), $this->equalTo(false))
            ->will($this->returnValue($this->getMock('\Phircy\Connection\IrcTransport')));

        $this->application->setConfig(array(
            'subscribers' => array($subscriber),
            'core.subscribers' => array($coreSubscriber),
            'listeners' => array('irc.privmsg' => array($listener)),
            'plugins' => array($plugin),
            'core.event_dispatcher' => $eventDispatcher,
            'networks' => $networks,
            'irc.connection_factory' => $ircFactory
        ));

        $this->application->setPhipe($phipe);
        $this->application->setConfigModelMapper($configModelMapper);

        $this->application->execute();
    }

    /**
     * @param string $host
     * @return Model\Connection
     */
    protected function createConnection($host) {
        $connection = new \Phircy\Model\Connection();
        $network = new \Phircy\Model\Network();
        $network->servers[] = new \Phircy\Model\Server($host);
        $connection->network = $network;

        return $connection;
    }
}