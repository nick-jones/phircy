<?php

namespace Phircy\Plugin;

use Phircy\Event\IrcEvent;
use Phircy\Matcher\Matcher;

/**
 * Invokable class which provides a simple way to wrap callbacks with matching. When an instance of this
 * class is invoked, the associated Matcher instance is called, and the contained callback is executed
 * if the Matcher returned TRUE.
 *
 * @package Phircy\Plugin
 */
class FilteringCallback {
    /**
     * @var Matcher
     */
    protected $matcher;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @param Matcher $matcher
     * @param callable $callback
     */
    public function __construct(Matcher $matcher, callable $callback) {
        $this->matcher = $matcher;
        $this->callback = $callback;
    }

    /**
     * Forwards the event onto the callback, if the event text matches. Note that this is only appropriate
     * for events providing text.
     *
     * @param IrcEvent $event
     */
    public function __invoke(IrcEvent $event) {
        $params = $event->getParams();
        $message = $params['text'];

        if ($this->matcher->matches($message)) {
            call_user_func($this->callback, $event);
        }
    }
}