# Phircy (Alpha)

[![Build Status](https://travis-ci.org/nick-jones/phircy.svg?branch=master)](https://travis-ci.org/nick-jones/phircy) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nick-jones/Phircy/badges/quality-score.png?s=1ce1dfac76359d4c17af86c18155fa12a7edc94a)](https://scrutinizer-ci.com/g/nick-jones/Phircy/) [![Code Coverage](https://scrutinizer-ci.com/g/nick-jones/Phircy/badges/coverage.png?s=f35f536f1ab3eb3f2917c3329591e804f9b4783e)](https://scrutinizer-ci.com/g/nick-jones/Phircy/)

Phircy is an IRC bot library, for PHP.

It is built on top of [nick-jones/Phipe](https://github.com/nick-jones/Phipe), various
[Phergie](https://github.com/phergie) components, and the
[symfony/EventDispatcher](https://github.com/symfony/EventDispatcher) Symfony component to provide an easy and robust
way to implement IRC applications.

__Caveat emptor__: This is not complete, and not production ready. Whilst a lot of the moving parts are functioning,
there is still work to be done. In particular, graceful error handling is very much lacking currently.

## Installation

To pull down dependencies and check version compatibility you will need to run [composer](http://getcomposer.org) in
the project root.

## Usage

Phircy requires a config file to be present in the project root. Simply `cp config.json.dist config.json`, and update
the following information:

* `networks`: fill in bot details. The servers field be set to a string, or an array if you wish to add fallback servers.
* `plugins`: provide the names of all the plugins you wish to be loaded.

Then simply execute `./phircy`

If you wish to run the application from other locations, or as a dependency of another project, then you simply need
to create an instance of the `Application` class, supplying connection details. Listeners, subscribers and plugins can
also be provided at this stage.

```php
$phircy = new \Phircy\Application([
    'networks' => [
        [
            'name' => 'EFnet',
            'nick' => '',
            'username' => '',
            'realname' => '',
            'servers' => 'irc.efnet.org',
            'channels' => ['#']
        ]
    ],
    'listeners' => [],
    'subscribers' => [],
    'plugins' => []
]);

$phircy->execute();
```

## Plugins

Currently plugins should be placed within the [`plugins/`](plugins/) (though this is liable to change at this stage.)

Plugins should extends `\Phircy\Plugin\Plugin`, be defined within the `\Phircy\Plugins` namespace and should register
matches and/or listeners for handling IRC events.

Matches are ways to register listeners which detect when a channel command occurs, e.g. `!flip`. The matching system
looks for `!` prefixes by default, so you only need indicate that `flip` be matched against.  Matches can be registered
by invoking `$this->match($eventName, callable $callback)` from within a plugin class. Regular expression based matching
is available via the `matchRegex()` method, and standard wildcard matching is available via the `matchPattern()`
method.

Listeners are of the same flavour as the standard listeners that can be supplied to Phircy. They can hook into
any flavour of events emitted by the application. Listeners can be registered via the listen method:
`$this->listen($eventName, callable $callback)`.

A basic example of a plugin that performs a "coin flip" when `!flip` is sent to a channel:

```php
<?php

namespace Phircy\Plugins;

class Flip extends \Phircy\Plugin\Plugin
{
    /**
     * Create a matcher for the !flip command.
     */
    public function __construct() {
        $this->match('flip', [$this, 'onFlip']);
    }

    /**
     * @param \Phircy\Event\TargetedIrcEvent $event
     */
    public function onFlip(\Phircy\Event\TargetedIrcEvent $event) {
        static $sides = ['heads', 'tails'];

        $params = $event->getParams();
        $result = $sides[mt_rand(0, 1)];
        $response = sprintf('flip result: %s', $result);

        $event->getTransport()
            ->writePrivmsg($params['receivers'], $response);
    }
}
```

## Unit Tests

The unit tests for Phircy are built with PHPUnit. The tests are located within the tests/unit/ directory, and
configured by phpunit.xml in the project root.

PHPUnit is listed as a development dependency for this project; as such, you can simply run `./vendor/bin/phpunit`
to execute the tests.
