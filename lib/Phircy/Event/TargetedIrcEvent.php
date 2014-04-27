<?php

namespace Phircy\Event;

use Phergie\Irc\Event\TargetedEventInterface;
use Phergie\Irc\Event\TargetedEventTrait;

/**
 * This flavour of event is triggered when the event contains a channel or user target.
 *
 * @package Phircy\Event
 */
class TargetedIrcEvent extends IrcEvent implements TargetedEventInterface
{
    /**
     * All relevant methods are provided by the Phergie TargetedEventTrait
     */
    use TargetedEventTrait;
}