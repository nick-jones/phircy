<?php

namespace Phircy\Plugin;

/**
 * @package Phircy\Plugin
 */
class PluginTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Plugin
     */
    protected $plugin;

    protected function setUp() {
        $this->plugin = $this->getMockForAbstractClass('\Phircy\Plugin\Plugin');
    }

    public function testMatch() {
        $method = $this->createMethod('match');
        $method->invoke($this->plugin, 'mock', function(){});

        $this->assertEquals(1, count($this->plugin->getListeners()));
    }

    public function testMatchPattern() {
        $method = $this->createMethod('matchPattern');
        $method->invoke($this->plugin, 'mock*', function(){});

        $this->assertEquals(1, count($this->plugin->getListeners()));
    }

    public function testMatchRegex() {
        $method = $this->createMethod('matchRegex');
        $method->invoke($this->plugin, 'mock.*', function(){});

        $this->assertEquals(1, count($this->plugin->getListeners()));
    }

    public function testGetListeners() {
        $this->assertEquals(array(), $this->plugin->getListeners());
    }

    /**
     * Creates a ReflectionMethod instance for the supplied method name. This is unfortunate, but seemingly
     * necessary; the Plugin interface is not public, but it is an interface to be used by plugin implementations.
     * Since we have no concrete plugins bundled with Phircy, it's impossible to test these methods without taking
     * this route.
     *
     * @param string $methodName
     * @return \ReflectionMethod
     */
    protected function createMethod($methodName) {
        $method = new \ReflectionMethod('\Phircy\Plugin\Plugin', $methodName);
        $method->setAccessible(true);

        return $method;
    }
}