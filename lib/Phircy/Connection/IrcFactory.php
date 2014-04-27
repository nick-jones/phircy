<?php

namespace Phircy\Connection;

use Phergie\Irc\GeneratorInterface;
use Phipe\Connection\Buffering\BufferingFactory;
use Phipe\Connection\Factory;

/**
 * Factory for IrcTransport instances.
 *
 * @package Phircy\Connection
 */
class IrcFactory extends BufferingFactory
{
    /**
     * @var \Phergie\Irc\GeneratorInterface
     */
    protected $generator;

    /**
     * @param Factory $factory
     * @param GeneratorInterface $generator
     */
    public function __construct(Factory $factory, GeneratorInterface $generator)
    {
        $this->generator = $generator;

        parent::__construct($factory);
    }

    /**
     * @param string $host
     * @param int $port
     * @param bool $ssl
     * @return IrcTransport
     */
    public function createConnection($host, $port, $ssl = false)
    {
        return new IrcTransport(
            $this->factory->createConnection($host, $port, $ssl),
            $this->generator
        );
    }
}