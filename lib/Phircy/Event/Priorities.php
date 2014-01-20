<?php

namespace Phircy\Event;

/**
 * Container for Event priority constants.
 *
 * @package Phircy\Event
 */
class Priorities {
    /**
     * All Phircy application listeners that must run before *all* other listeners should use this priority level.
     */
    const PHIRCY_PRE = 3;

    /**
     * User listeners that wish to run before Phircy and other user listeners should use this level.
     */
    const USER_PRE = 2;

    /**
     * All Phircy application listeners that wish to run at a normal point in time should use this level.
     */
    const PHIRCY_STANDARD = 1;

    /**
     * User listeners which do not require early or late running should use this level.
     */
    const USER_STANDARD = 0;

    /**
     * User listeners to be executes after all standard listeners.
     */
    const USER_POST = -1;

    /**
     * Phircy application listeners are to be run
     */
    const PHIRCY_POST = -2;
}