<?php

namespace REW\Seed\Faker\Company;

/**
 * AgentFaker
 * @package REW\Seed\Faker
 */
class AgentFaker extends AccountFaker
{

    /**
     * Agent image width (in pixels)
     * @var int
     */
    const AGENT_IMAGE_WIDTH = 640;

    /**
     * Agent image height (in pixels)
     * @var int
     */
    const AGENT_IMAGE_HEIGHT = 480;

    /**
     * Agent image category
     * @var string
     */
    const AGENT_IMAGE_CATEGORY = null;

    /**
     * Agent job titles
     * @var array
     */
    protected $jobTitles = [
        'REALTOR®',
        'Agent',
        'Real Estate Advisor',
        'Real Estate Professional',
        'Seller Agent',
        'Listing Partner',
        'Exclusive Buyer Specialist',
        'Listing Coordinator',
        'Short Sale Listing Specialist'
    ];

    /**
     * Agent boss titles
     * @var array
     */
    protected $bossTitles = [
        'CEO',
        'CTO',
        'Broker',
        'Director of Sales',
        'Director of Marketing',
        'Director of Operations'
    ];

    /**
     * Fake agent
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        $account = parent::getFakeData();
        $display = $faker->randomElement(['Y', 'N']);
        $feature = $display === 'Y' ? $faker->randomElement(['Y', 'N']) : 'N';
        return $account + [
            'office_phone'    => $faker->optional()->phoneNumber,
            'home_phone'      => $faker->optional()->phoneNumber,
            'cell_phone'      => $faker->optional()->phoneNumber,
            'fax'             => $faker->optional()->phoneNumber,
            'website'         => $faker->optional()->url,
            'remarks'         => $faker->optional()->realText(),
            'image'           => $this->getFakeAgentImage(),
            'title'           => $this->getFakeAgentTitle(),
            'display'         => $display,
            'display_feature' => $feature
        ] + $this->getFakeTimestamps();
    }

    /**
     * Get fake agent title
     * @return string
     */
    public function getFakeAgentTitle()
    {
        $faker = $this->getFaker();
        // No title
        if ($faker->boolean(25)) {
            return '';
        // Boss title
        } else if ($faker->boolean) {
            if ($title = array_shift($this->bossTitles)) {
                return $title;
            }
        }
        // Job title
        return $this->jobTitles[array_rand($this->jobTitles)];
    }

    /**
     * Get fake agent image
     * @return string
     */
    public function getFakeAgentImage()
    {
        $faker = $this->getFaker();
        return $faker->boolean ? basename(
            $faker->image(
                $this->getPathToAgentUploads(),
                self::AGENT_IMAGE_WIDTH,
                self::AGENT_IMAGE_HEIGHT,
                self::AGENT_IMAGE_CATEGORY
            )
        ) : '';
    }

    /**
     * Get path to agent uploads
     * @return string
     */
    public function getPathToAgentUploads()
    {
        return sprintf('%s/agents', $this->getPathToUploads());
    }
}
