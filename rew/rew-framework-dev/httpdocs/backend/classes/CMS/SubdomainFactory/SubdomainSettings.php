<?php

namespace REW\Backend\CMS\SubdomainFactory;

use REW\Backend\CMS\Interfaces\SubdomainFactory\SubdomainSettingsInterface;
use REW\Backend\CMS\Interfaces\SubdomainFactoryInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\SettingsInterface;

abstract class SubdomainSettings implements SubdomainSettingsInterface
{
    /**
     * Id of Subdomain
     * @var int $id
     */
    private $id;

    /**
     * @Type of Subdomain
     * @var string $type
     */
    private $type;

    /**
     * Link To Subdomain
     * @var string $link
     */
    private $link;

    /**
     * Title of Subdomain
     * @var string $title
     */
    private $title;

    /**
     * @var SubdomainFactoryInterface
     */
    private $subdomainFactory;

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @var IDXFactoryInterface
     */
    private $idxFactory;

    /**
     * SubdomainSettings constructor.
     * @param SubdomainFactoryInterface $subdomainFactory
     * @param SettingsInterface $settings
     * @param IDXFactoryInterface $idxFactor
     * @param string $type
     */
    public function __construct(
        SubdomainFactoryInterface $subdomainFactory,
        SettingsInterface $settings,
        IDXFactoryInterface $idxFactory,
        $type
    ) {
        $this->subdomainFactory = $subdomainFactory;
        $this->settings = $settings;
        $this->idxFactory = $idxFactory;
        $this->type = $type;
    }

    /**
     * Gets the Subdomains Id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the Subdomains Type
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets the Subdomains Link
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Gets the Subdomain Title
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param int $id
     * @throws \Exception if $id is non-numeric
     */
    public function setId($id)
    {
        if (!is_numeric($id)) {
            throw new \Exception('Subdomain id must be numeric');
        }
        $this->id = $id;
    }

    /**
     * @param string $type
     * @throws \Exception if $type is an invalid subdomain type
     */
    public function setType($type)
    {
        if (!in_array($type, $this->subdomainFactory->getTypes())) {
            throw new \Exception(sprintf('%s is an invalid subdomain type.', $type));
        }
        $this->type = $type;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @inheritDoc
     */
    public function getSubdomainIdFromArray(array $context)
    {
        $type = $this->getType();
        $id = isset($context[$type]) ? $context[$type] : null;

        if ($id) {
            return $id;
        }

        return false;
    }

    /**
     * Find the default feed on the site, given a list of possible feeds.
     *
     * @param array $feeds
     * @return mixed|string
     */
    public function getDefaultFeedFromArray($feeds)
    {
        // Load List Of Available IDX Feeds
        $idx_feeds = !empty($this->settings['IDX_FEEDS'])
            ? array_keys($this->settings['IDX_FEEDS']) : array($this->settings['IDX_FEEDS']);

        $individual_feeds = $this->idxFactory->parseFeeds($idx_feeds);


        // Set The List Of Subdomain Feeds To the List Of Feeds That Are Both Available And What The Agent Has Access To
        $team_feeds = array_intersect($feeds, $individual_feeds);

        $default_feed = '';

        // If The Default IDX Is Not Available For The Team Site
        if (!in_array($this->settings['IDX_FEED'], $team_feeds)) {
            // Switch To First Available IDX On The List
            $default_feed = array_shift($team_feeds);

            // Check If First Available IDX Is Part Of Comingled Feed And If So, Switch To That Instead
            foreach ($idx_feeds as $feed) {
                $idx = $this->idxFactory->getIdx($feed);

                if ($idx->isCommingled() && $idx->containsFeed($default_feed)) {
                    $default_feed = $feed;
                    break;
                }
            }
        }

        return $default_feed;
    }
}
