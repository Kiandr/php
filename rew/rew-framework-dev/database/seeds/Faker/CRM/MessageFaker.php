<?php

namespace REW\Seed\Faker\CRM;

use REW\Seed\Faker\AbstractFaker;

/**
 * MessageFaker
 * @package REW\Seed\Faker
 */
class MessageFaker extends AbstractFaker
{

    /**
     * Fake message
     * @return array
     */
    public function getFakeData()
    {
        $faker = $this->getFaker();
        $timestamp = $faker->dateTimeThisYear();
        return [
            //'user_id' => NULL,
            //'agent_id' => NULL,
            'subject' => $faker->sentence(),
            'message' => $faker->paragraphs(rand(1, 2), true), //$faker->realText(),
            'timestamp' => $timestamp->format('Y-m-d H:i:s'),
            //'category' => NULL,
            'sent_from' => 'lead',
            //'user_alert' => 'Y',
            //'user_read' => 'N',
            'agent_read' => 'N',
            'user_del' => 'N',
            'reply' => 'N'
        ];
    }
}
