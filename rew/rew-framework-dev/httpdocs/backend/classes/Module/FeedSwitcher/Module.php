<?php

namespace REW\Backend\Module\FeedSwitcher;

use REW\Core\Interfaces\SettingsInterface;
use Backend_Agent;
use Backend_Team;

/**
 * Module
 * @package REW\Backend\Module\FeedSwitcher
 */
class Module
{

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @param SettingsInterface $settings
     */
    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return string
     */
    public function getIdxFeed()
    {
        return $this->settings->IDX_FEED;
    }

    /**
     * @return array
     */
    public function getIdxFeeds()
    {
        if(!empty($this->settings->IDX_FEEDS)) {
            $idxFeeds = $this->settings->IDX_FEEDS;
        } else if (!empty($this->settings->IDX_FEED)) {
            return ["name" => [
                "link" => $this->settings->IDX_FEED,
                "title" => strtoupper(str_replace(['_', '-'], ' ', $this->settings->IDX_FEED))
            ]];
        }
        if (empty($idxFeeds) || !is_array($idxFeeds)) {
            return [];
        }
        foreach ($idxFeeds as $idxLink => $idxFeed) {
            $idxFeeds[$idxLink] = [
                'link' => $idxLink,
                'title' => $idxFeed['title']
            ];
        }
        return $idxFeeds;
    }

    /**
     * @param Backend_Agent $agent
     * @return array
     */
    public function getAgentFeeds(Backend_Agent $agent)
    {
        $idxFeeds = $this->getIdxFeeds();
        $agentFeeds = $agent['cms_idxs'];
        if (empty($agentFeeds)) {
            return [];
        }
        $agentFeeds = explode(',', $agentFeeds);
        return $this->filterIdxFeeds($idxFeeds, $agentFeeds);
    }

    /**
     * @param Backend_Team $team
     * @return array
     */
    public function getTeamFeeds(Backend_Team $team)
    {
        $idxFeeds = $this->getIdxFeeds();
        $teamFeeds = $team['subdomain_idxs'];
        if (empty($teamFeeds)) {
            return [];
        }
        $teamFeeds = explode(',', $teamFeeds);
        return $this->filterIdxFeeds($idxFeeds, $teamFeeds);
    }

    /**
     * @param array $idxFeeds
     * @param array $filterFeeds
     * @return array
     */
    protected function filterIdxFeeds(array $idxFeeds, array $filterFeeds)
    {
        return array_filter($idxFeeds, function ($idxFeed) use ($filterFeeds) {
            return in_array($idxFeed['link'], $filterFeeds);
        });
    }
}
