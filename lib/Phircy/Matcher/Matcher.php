<?php

namespace Phircy\Matcher;

/**
 * Matcher implementations should indicate whether supplied messages match their expectations.
 *
 * @package Phircy\Matcher
 */
interface Matcher
{
    /**
     * Indicates whether the supplied message matches the implementations expectations.
     *
     * @param string $message
     * @return bool
     */
    public function matches($message);
}