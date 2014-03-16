<?php

namespace Phircy\Connection;

use Phergie\Irc\GeneratorInterface;
use Phipe\Connection\Connection;

/**
 * The IrcTransport class provides transport connectivity to a remote endpoint (i.e. an IRC server). This class extends
 * a Phipe Connection class; note that the Phipe "Connection" terminology has not been retained, to avoid confusion
 * with our own Model\Connection class.
 *
 * A large number of "virtual" methods are provided by virtue of __call magic method. These are implemented by
 * requesting the IRC line based on the method name and arguments from a Phergie IRC generator instance. The methods
 * are provided for user convenience; they are not a requirement - if you wish to write raw messages, simply call
 * the "write()" method.
 *
 * @method void writePass(string $password)
 * @method void writeNick(string $nickname, int $hopcount = null)
 * @method void writeUser(string $username, string $hostname, string $servername, string $realname)
 * @method void writeServer(string $servername, int $hopcount, string $info)
 * @method void writeOper(string $user, string $password)
 * @method void writeQuit(string $message = null)
 * @method void writeSquit(string $server, string $comment)
 * @method void writeJoin(string $channels, string $keys = null)
 * @method void writePart(string $channels)
 * @method void writeMode(string $target, string $mode, string $param = null)
 * @method void writeTopic(string $channel, string $topic = null)
 * @method void writeNames(string $channels)
 * @method void writeList(string $channels = null, string $server = null)
 * @method void writeInvite(string $nickname, string $channel)
 * @method void writeKick(string $channel, string $user, string $comment = null)
 * @method void writeVersion(string $server = null)
 * @method void writeStats(string $query, string $server = null)
 * @method void writeLinks(string $servermask = null, string $remoteserver = null)
 * @method void writeTime(string $server = null)
 * @method void writeConnect(string $targetserver, int $port = null, string $remoteserver = null)
 * @method void writeTrace(string $server = null)
 * @method void writeAdmin(string $server = null)
 * @method void writeInfo(string $server = null)
 * @method void writePrivmsg(string $receivers, $text)
 * @method void writeNotice(string $nickname, string $text)
 * @method void writeWho(string $name, string $o = null)
 * @method void writeWhois(string $server, string $nickmasks)
 * @method void writeWhowas(string $nickname, int $count = null, string $server = null)
 * @method void writeKill(string $nickname, string $comment)
 * @method void writePing(string $server1, string $server2 = null)
 * @method void writePong(string $daemon, string $daemon2 = null)
 * @method void writeError(string $message)
 * @method void writeAway(string $message = null)
 * @method void writeRehash()
 * @method void writeRestart()
 * @method void writeSummon(string $user, string $server = null)
 * @method void writeUsers(string $server = null)
 * @method void writeWallops(string $text)
 * @method void writeUserhost(string $nickname1, string $nickname2 = null, string $nickname3 = null, string $nickname4 = null, string $nickname5 = null)
 * @method void writeIson(string $nicknames)
 * @package Phircy\Connection
 */
class IrcTransport extends \Phipe\Connection\Buffering\BufferingConnection {
    /**
     * @var \Phergie\Irc\GeneratorInterface
     */
    protected $generator;

    /**
     * @param Connection $connection
     * @param GeneratorInterface $generator
     */
    public function __construct(Connection $connection = NULL, GeneratorInterface $generator = NULL) {
        $this->generator = $generator;

        parent::__construct($connection);
    }

    /**
     * Here we handle any write* methods that are not defined by the class.
     *
     * @param string $name
     * @param array $arguments
     * @throws \BadMethodCallException
     */
    public function __call($name, array $arguments) {
        if (preg_match('#^write(.+)$#', $name, $matches)) {
            $this->writeCommand($matches[1], $arguments);
            return;
        }

        throw new \BadMethodCallException('Undefined method');
    }

    /**
     * @param string $command The name of the command, e.g. 'ping'
     * @param array $arguments
     * @throws \InvalidArgumentException
     */
    public function writeCommand($command, array $arguments) {
        $generatorMethod = sprintf('irc%s', ucfirst(strtolower($command)));

        if (!method_exists($this->generator, $generatorMethod)) {
            throw new \InvalidArgumentException(sprintf('Command "%s" does not exist', $command));
        }

        $ircMessage = call_user_func_array(array($this->generator, $generatorMethod), $arguments);

        $this->write($ircMessage);
    }

    /**
     * @return array
     */
    public function readLines() {
        $data = $this->read();
        $lines = preg_split("#\r?\n#", $data, -1, PREG_SPLIT_NO_EMPTY);

        return $lines;
    }
}

