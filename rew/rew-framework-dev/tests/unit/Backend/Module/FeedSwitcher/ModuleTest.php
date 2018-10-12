<?php

namespace REW\Test\Backend\Module\FeedSwitcher;

use REW\Backend\Module\FeedSwitcher\Module;
use REW\Core\Interfaces\SettingsInterface;
use Codeception\Test\Unit;
use Backend_Agent;
use Backend_Team;
use Mockery;

class FeedSwitcherModuleTest extends Unit
{

    /**
     * @var Module
     */
    protected $module;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var string
     */
    const IDX_FEED = 'foo';

    /**
     * @var array
     */
    const IDX_FEEDS = [
        'foo' => ['title' => 'Foo'],
        'bar' => ['title' => 'Bar'],
        'qaz' => ['title' => 'Qaz']
    ];

    /**
     * @return void
     */
    protected function _before()
    {
        $this->settings = Mockery::mock(SettingsInterface::class);
        $this->settings->IDX_FEEDS = self::IDX_FEEDS;
        $this->settings->IDX_FEED = self::IDX_FEED;
    }

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }

    /**
     * @covers \REW\Backend\Module\FeedSwitcher\Module::__construct
     */
    public function testConstruct()
    {
        $module = new Module($this->settings);
        $this->assertInstanceOf(Module::class, $module);
        return $module;
    }

    /**
     * @covers \REW\Backend\Module\FeedSwitcher\Module::getIdxFeed
     * @depends testConstruct
     * @param Module $module
     */
    public function testGetIdxFeed(Module $module)
    {
        $this->assertSame('foo', $module->getIdxFeed());
    }

    /**
     * @covers \REW\Backend\Module\FeedSwitcher\Module::getIdxFeeds
     * @depends testConstruct
     * @param Module $module
     */
    public function testGetIdxFeeds(Module $module)
    {
        $this->assertSame([
            'foo' => ['link' => 'foo', 'title' => 'Foo'],
            'bar' => ['link' => 'bar', 'title' => 'Bar'],
            'qaz' => ['link' => 'qaz', 'title' => 'Qaz']
        ], $module->getIdxFeeds());
    }

    /**
     * @covers \REW\Backend\Module\FeedSwitcher\Module::getAgentFeeds
     * @depends testConstruct
     * @param Module $module
     */
    public function testGetAgentFeeds(Module $module)
    {

        // Test agent with no feeds
        $agent = Mockery::mock(Backend_Agent::class);
        $agent->shouldReceive('offsetGet')->andReturn('');
        $this->assertEmpty($module->getAgentFeeds($agent));

        // Test agent with single feed
        $agent = Mockery::mock(Backend_Agent::class);
        $agent->shouldReceive('offsetGet')->andReturn('foo');
        $this->assertSame(['foo' => ['link' => 'foo', 'title' => 'Foo']], $module->getAgentFeeds($agent));
    }

    /**
     * @covers \REW\Backend\Module\FeedSwitcher\Module::getTeamFeeds
     * @depends testConstruct
     * @param Module $module
     */
    public function testGetTeamFeeds(Module $module)
    {

        // Test team with no feeds
        $team = Mockery::mock(Backend_Team::class);
        $team->shouldReceive('offsetGet')->andReturn('');
        $this->assertEmpty($module->getTeamFeeds($team));

        // Test team with single feed
        $team = Mockery::mock(Backend_Team::class);
        $team->shouldReceive('offsetGet')->andReturn('bar');
        $this->assertSame(['bar' => ['link' => 'bar', 'title' => 'Bar']], $module->getTeamFeeds($team));
    }
}
