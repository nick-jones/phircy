<?php

namespace Phircy\Parser;

use Phergie\Irc\Parser;

/**
 * IrcParser implementation that uses Phergie\Irc\Parser for translation of IRC lines into structured details.
 *
 * @package Phircy\Parser
 */
class PhergieIrcParser implements IrcParser {
    /**
     * @var Parser
     */
    protected $phergieParser;

    /**
     * @param $line
     * @return array|null
     */
    public function parse($line) {
        return $this->getPhergieParser()
            ->parse($line . "\r\n");
    }

    /**
     * @return Parser
     */
    public function getPhergieParser() {
        if (!$this->phergieParser) {
            $this->phergieParser = new Parser();
        }

        return $this->phergieParser;
    }

    /**
     * @param Parser $parser
     */
    public function setPhergieParser(Parser $parser) {
        $this->phergieParser = $parser;
    }
}