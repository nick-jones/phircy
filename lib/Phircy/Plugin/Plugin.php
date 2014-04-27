<?php

namespace Phircy\Plugin;

use Phircy\Matcher\CommandMatcher;
use Phircy\Matcher\PatternMatcher;
use Phircy\Matcher\RegexMatcher;

/**
 * Skeleton of a plugin, this provides methods for listening to events. All plugin implementations
 * must extend this class. Plugins are similar to simple listeners and subscribers, but provide a more
 * convenient way to combine functionality into a single "package".
 *
 * @package Phircy\Plugin
 */
abstract class Plugin
{
    /**
     * Prefix for command related matching.
     *
     * @var string
     */
    protected $prefix = '!';

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * This allows plugins to register a command to be watched for. If the command is seen, the associated
     * callback (as supplied) is executed.
     *
     * @param string $command
     * @param callable $callback
     */
    protected function match($command, callable $callback)
    {
        $matcher = new CommandMatcher($this->prefix . $command);
        $this->listen('irc.privmsg', new FilteringCallback($matcher, $callback));
    }

    /**
     * This allows patterns to watch for text based on wildcard pattern expressions. Glob-style patterns
     * are permitted, with the following constructs available:
     *
     *  - Wildcard multiple: *
     *  - Wildcard single: ?
     *  - Grouping, ranges, classes: [abc], [a-z], [!x], [^x], [[:alpha:]]
     *
     * @param string $pattern Glob-style wildcard pattern
     * @param callable $callback Callback to be invoked on successful match
     */
    protected function matchPattern($pattern, callable $callback)
    {
        $matcher = new PatternMatcher($pattern);
        $this->listen('irc.privmsg', new FilteringCallback($matcher, $callback));
    }

    /**
     * This allows plugins to watch for text based on a regular expression. The provided callback will be
     * called when the regular expression matches against a message from IRC.
     *
     * @param string $pattern
     * @param callable $callback
     */
    protected function matchRegex($pattern, callable $callback)
    {
        $matcher = new RegexMatcher($pattern);
        $this->listen('irc.privmsg', new FilteringCallback($matcher, $callback));
    }

    /**
     * Plugins can listen for any Phircy events (including core ones, if they so wish). When these events
     * occur, the associated callback will be executed.
     *
     * @param $eventName
     * @param callable|FilteringCallback $callback
     */
    protected function listen($eventName, callable $callback)
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = array();
        }

        $this->listeners[$eventName][] = $callback;
    }

    /**
     * @return array
     */
    public function getListeners()
    {
        return $this->listeners;
    }
}