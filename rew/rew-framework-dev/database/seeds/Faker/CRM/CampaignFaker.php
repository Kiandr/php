<?php

namespace REW\Seed\Faker\CRM;

use REW\Seed\Faker\AbstractFaker;

/**
 * CampaignFaker
 * @package REW\Seed\Faker
 */
class CampaignFaker extends AbstractFaker
{

    /**
     * Fake campaign
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        $name = $faker->catchPhrase;
        $sender = $faker->randomElement(['admin', 'agent', 'custom']);
        $sender_name = $sender === 'custom' ? $faker->name : null;
        $sender_email = $sender === 'custom' ? $faker->safeEmail : null;
        $timestamp = $faker->dateTimeThisYear();
        $starts = $faker->optional()->dateTimeThisYear();
        return [
            //'agent_id' => NULL,
            //'tempid' => NULL,
            'name' => $name,
            'sender' => $sender,
            'sender_name' => $sender_name,
            'sender_email' => $sender_email,
            'description' => $faker->optional()->sentence,
            'starts' => $starts ? $starts->format('Y-m-d H:i:s') : null,
            'active' => $faker->randomElement(['Y', 'N']),
            'timestamp' => $timestamp->format('Y-m-d H:i:s')
        ];
    }
}
