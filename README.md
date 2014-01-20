# Phircy (Alpha)

Phircy is an IRC client library, for PHP.

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

Phircy requires a config file to be present in the project root. Simply `cp config.json.dist config.json` and fill in
connection details for all the networks you wish to connect to.

Then simply execute `./phircy`

Please note that currently there are no mechanisms for automatically registering listeners, so the IRC session will
simply sit idle.

If you wish to run the application from other locations, or as a dependency of another project, then you simply need
to create an instance of the Application class, supplying connection details. Listeners can also be provided at this
stage.

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
    'listeners' => $listeners
]);

$phircy->execute();
```

## Unit Tests

The unit tests for Phircy are built with PHPUnit. The tests are located within the tests/unit/ directory, and
configured by phpunit.xml in the project root.

PHPUnit is listed as a development dependency for this project; as such, you can simply run `./vendor/bin/phpunit`
to execute the tests.
