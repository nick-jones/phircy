<?php

namespace Phircy\Handler;

class PrintingUpdateHandlerTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var PrintingUpdateHandler
     */
    protected $handler;

    /**
     * @var resource
     */
    protected $handle;

    const PREFIX_REGEX = '\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] \(Mock\)';

    protected function setUp() {
        $this->handle = fopen('php://memory', 'rw');
        $this->handler = new PrintingUpdateHandler($this->handle);
    }

    protected function tearDown() {
        fclose($this->handle);
    }

    /**
     * @return string
     */
    protected function getHandleContents() {
        return stream_get_contents($this->handle, -1, 0);
    }

    public function testProcessConnect() {
        $connections = new \SplObjectStorage();
        $connection = $this->createMockConnection();

        $this->handler->processConnect($connections, $connection);

        $output = $this->getHandleContents();
        $this->assertRegExp(sprintf('/%s Connected!/', self::PREFIX_REGEX), $output);
    }

    public function testProcessDisconnect() {
        $connections = new \SplObjectStorage();
        $connection = $this->createMockConnection();

        $this->handler->processDisconnect($connections, $connection);

        $output = $this->getHandleContents();
        $this->assertRegExp(sprintf('/%s Disconnected!/', self::PREFIX_REGEX), $output);
    }

    public function testProcessRead() {
        $connections = new \SplObjectStorage();
        $connection = $this->createMockConnection();
        $message = 'NICK :Mock';

        $this->handler->processRead($connections, $connection, array($message));

        $output = $this->getHandleContents();
        $this->assertRegExp(sprintf('/%s ← %s/', self::PREFIX_REGEX, $message), $output);
    }

    public function testProcessWrite() {
        $connections = new \SplObjectStorage();
        $connection = $this->createMockConnection();
        $message = 'PART :#Mock';

        $this->handler->processWrite($connections, $connection, array($message));

        $output = $this->getHandleContents();
        $this->assertRegExp(sprintf('/%s → %s/', self::PREFIX_REGEX, $message), $output);
    }

    /**
     * @return \Phircy\Model\Connection
     */
    protected function createMockConnection() {
        $connection = new \Phircy\Model\Connection();
        $connection->network = new \Phircy\Model\Network();
        $connection->network->name = 'Mock';

        return $connection;
    }
}