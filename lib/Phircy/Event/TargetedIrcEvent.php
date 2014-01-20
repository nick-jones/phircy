<?php

namespace Phircy\Event;

/**
 * This flavour of event is triggered when the event contains a channel or user target.
 *
 * @package Phircy\Event
 */
class TargetedIrcEvent extends IrcEvent implements \Phergie\Irc\Event\TargetedEventInterface {
    /**
     * All relevant methods are provided by the Phergie TargetedEventTrait
     */
    use \Phergie\Irc\Event\TargetedEventTrait;
}