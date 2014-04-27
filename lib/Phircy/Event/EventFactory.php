<?php

namespace Phircy\Event;

/**
 * Factory class for retrieving Event instances.
 *
 * @package Phircy\Event
 */
class EventFactory
{
    /**
     * Construct an Event instance based on parser output. This is based on Phergie\Irc\Event\ParserConverter, but is
     * tailored to use the Phircy flavour of Event classes.
     *
     * @param array $parserOutput
     * @return IrcEvent|TargetedIrcEvent
     */
    public function createFromParserOutput(array $parserOutput)
    {
        if (isset($parserOutput['targets'])) {
            $event = new TargetedIrcEvent();
            $event->setTargets($parserOutput['targets']);
        } else {
            $event = new IrcEvent();
        }

        $event->setCommand($parserOutput['command']);
        $event->setParams($parserOutput['params']);
        $event->setMessage($parserOutput['message']);

        return $event;
    }
}