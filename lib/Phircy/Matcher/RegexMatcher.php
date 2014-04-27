<?php

namespace Phircy\Matcher;

/**
 * This Match implementation allows for user-supplied regular expressions to be used for matching purposes.
 *
 * @package Phircy\Matcher
 */
class RegexMatcher implements Matcher
{
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @param string $message
     * @return bool
     */
    public function matches($message)
    {
        return (bool)preg_match($this->pattern, $message);
    }
}