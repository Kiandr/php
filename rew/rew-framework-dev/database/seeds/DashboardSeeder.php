<?php

use REW\Phinx\Seed\AbstractSeed;
use REW\Seed\Faker\CRM\FormFaker;
use REW\Seed\Faker\CRM\LeadFaker;
use REW\Seed\Faker\CRM\MessageFaker;

/**
 * DashboardSeeder - populate data for last 3 days
 *  - Add unassigned leads (every 1 hour)
 *  - Add lead inquiries (every 2 hours)
 *  - Add lead messages (every 3 hours)
 */
class DashboardSeeder extends AbstractSeed
{

    /**
     * Days of dashboard history
     * @var string
     */
    const DASHBOARD_DAYS = '-3 days';

    /**
     * Frequency of new leads
     * @var string
     */
    const REGISTER_FREQUENCY = '-1 hour';

    /**
     * Frequency of lead inquiries
     * @var string
     */
    const INQUIRY_FREQUENCY = '-2 hours';

    /**
     * Frequency of lead messages
     * @var string
     */
    const MESSAGE_FREQUENCY = '-3 hours';

    /**
     * Fake dashboard data
     * @return void
     */
    public function run()
    {

        // Create faker using DI container
        $container = Container::getInstance();
        $leadFaker = $container->make(LeadFaker::class);
        $formFaker = $container->make(FormFaker::class);
        $messageFaker = $container->make(MessageFaker::class);

        // Add leads
        $leadIds = [];
        $now = new DateTime('now');
        $end = new DateTime(self::DASHBOARD_DAYS);
        for ($d = $now; $now >= $end; $d->modify(self::REGISTER_FREQUENCY)) {
            // Generate lead
            $lead = $leadFaker->make([
                'agent' => 1,
                'status' => 'unassigned',
                'timestamp_assigned' => $d->format('Y-m-d H:i:s')
            ]);

            // Insert lead row to database
            $this->insert('users', $lead);
            $leadIds[] = $this->lastInsertId();
        }

        // Add lead inquiries
        $now = new DateTime('now');
        $end = new DateTime(self::DASHBOARD_DAYS);
        for ($d = $now; $now >= $end; $d->modify(self::INQUIRY_FREQUENCY)) {
            // Generate lead inquiry
            $form = $formFaker->make([
                'user_id' => $leadIds[array_rand($leadIds)],
                'timestamp' => $d->format('Y-m-d H:i:s')
            ]);

            // Insert form row to database
            $this->insert('users_forms', $form);
        }

        // Add lead messages
        $now = new DateTime('now');
        $end = new DateTime(self::DASHBOARD_DAYS);
        for ($d = $now; $now >= $end; $d->modify(self::MESSAGE_FREQUENCY)) {
            // Generate lead message
            $message = $messageFaker->make([
                'agent_id' => 1,
                'user_id' => $leadIds[array_rand($leadIds)],
                'timestamp' => $d->format('Y-m-d H:i:s')
            ]);

            // Insert message4 row to database
            $this->insert('users_messages', $message);
        }
    }
}
