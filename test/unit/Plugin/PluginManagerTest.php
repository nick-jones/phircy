<?php

namespace Phircy\Plugin;

/**
 * @package Phircy\Plugin
 */
class PluginManagerTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var PluginManager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $pluginName = 'PhircyMockPlugin';

    /**
     * @var string
     */
    protected $pluginPath;

    protected function setUp() {
        $fileName = sprintf('%s.php', $this->pluginName);
        $this->pluginPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;

        $plugins = array(
            $this->pluginName,
            $this->getMock('\Phircy\Plugin\Plugin')
        );

        $this->manager = new PluginManager($plugins, dirname($this->pluginPath));
    }

    /**
     * Copies our mock plugin into the temporary plugin path.
     */
    protected function setUpPlugin() {
        $source = implode(DIRECTORY_SEPARATOR, array(
            __DIR__, '..', '..', 'helper', 'MockPluginTemplate.txt'
        ));

        $contents = sprintf(file_get_contents($source), $this->pluginName);
        file_put_contents($this->pluginPath, $contents);
    }

    protected function tearDown() {
        if (is_file($this->pluginPath)) {
            unlink($this->pluginPath);
        }
    }

    public function testLoad() {
        $this->setUpPlugin();

        $this->manager->load();

        $plugins = $this->manager->getPlugins();

        $this->assertEquals(2, count($plugins));
        $this->assertEquals('Phircy\Plugins\PhircyMockPlugin', get_class($plugins[0]));
    }

    public function testLoad_MissingFile() {
        $this->setExpectedException('\Phircy\Plugin\LoadException', 'Plugin file could not be found');

        $this->pluginName = 'PhircyMockFoo';
        $this->setUp();

        $this->manager->load();
    }

    public function testLoad_MissingClass() {
        $this->setExpectedException(
            '\Phircy\Plugin\LoadException',
            'Plugin class "\Phircy\Plugins\PhircyMockBar" could not be resolved'
        );

        $this->pluginName = 'PhircyMockBar';
        $this->setUp();

        // Clear the files contents, so no class can be loaded
        file_put_contents($this->pluginPath, '');

        $this->manager->load();
    }

    public function testGetPlugins() {
        $this->assertEquals(2, count($this->manager->getPlugins()));
    }
}