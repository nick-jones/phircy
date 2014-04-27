<?php

namespace Phircy\Parser;

/**
 * IrcParser implementations should parse string IRC lines into a structured array based representations.
 *
 * @package Phircy\Parser
 */
interface IrcParser
{
    /**
     * Parse an IRC string into an array. The array should contain the following keys (as supplied by Phergie):
     *
     *  - "command": the IRC command, e.g. "PING"
     *  - "params": the parameters of the command, e.g. the server name of a PING request.
     *  - "message": the raw IRC line
     *  - "targets" (optional): the channels or users targeted by the command
     *
     * @param string $line
     * @return array|null
     */
    public function parse($line);
}