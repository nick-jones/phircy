<?php

namespace Phircy\Matcher;

/**
 * This match implementation looks for commands. That is, IRC triggers, e.g. !vote, !flip, etc. The command
 * is supplied to the class (prefix inclusive), and it will indicate whether an IRC message matches it.
 *
 * Note that this supports matching against both single and multi-word lines, e.g. with !vote supplied,
 * the following would both match: "!vote", "!vote bob", but "!votes" would not.
 *
 * @package Phircy\Matcher
 */
class CommandMatcher implements Matcher
{
    /**
     * @var string
     */
    protected $command;

    /**
     * @param string $command
     */
    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * @param string $message
     * @return bool
     */
    public function matches($message)
    {
        return $message === $this->command
            || strpos($message, $this->command . ' ') !== false;
    }
}