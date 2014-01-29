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

    public function testGetListeners() {
        $this->assertEquals(array(), $this->plugin->getListeners());
    }
}