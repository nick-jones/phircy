<?php

require_once __DIR__ . '/../vendor/autoload.php';

$listeners = [
    'irc.privmsg' => [
        function(\Phircy\Event\TargetedIrcEvent $event) {
            $params = $event->getParams();

            if (!preg_match('/^say (.+)/', $params['text'], $matches)) {
                return;
            }

            $event->getConnection()
                ->transport
                ->writePrivmsg($params['receivers'], $matches[1]);
        }
    ]
];

$phircy = new \Phircy\Application([
    'networks' => [
        [
            'name' => 'EFnet',
            'nick' => 'monkey_eh',
            'username' => 'monkey',
            'realname' => 'monkey',
            'servers' => 'irc.efnet.org',
            'channels' => [
                '#monkey_casita'
            ]
        ]
    ],
    'listeners' => $listeners
]);

$phircy->execute();