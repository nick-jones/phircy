<?php

namespace Phircy\Matcher;

use Globby\Pattern;

/**
 * Matcher implementation that accepts glob-style wildcard patterns. These are converted to regular
 * expressions via the Globby library; everything else is handled by the RegexMatcher implementation.
 *
 * @package Phircy\Matcher
 */
class PatternMatcher extends RegexMatcher
{
    /**
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $pattern = new Pattern($pattern);

        parent::__construct($pattern->toRegex());
    }
}