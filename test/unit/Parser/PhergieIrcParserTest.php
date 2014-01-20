<?php

namespace Phircy\Parser;

class PhergieIrcParserTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var PhergieIrcParser
     */
    protected $parser;

    protected function setUp() {
        $this->parser = new PhergieIrcParser();
    }

    public function testParse() {
        $line = 'NICK :Mock';
        $output = array('command' => 'NICK', 'params' => array('Mock'));

        $phergieParser = $this->getMock('\Phergie\Irc\Parser');

        $phergieParser->expects($this->once())
            ->method('parse')
            ->with($this->equalTo($line . "\r\n"))
            ->will($this->returnValue($output));

        $this->parser->setPhergieParser($phergieParser);

        $result = $this->parser->parse($line);
        $this->assertEquals($output, $result);
    }

    public function testGetPhergieParser() {
        $this->assertInstanceOf('\Phergie\Irc\Parser', $this->parser->getPhergieParser());
    }
}