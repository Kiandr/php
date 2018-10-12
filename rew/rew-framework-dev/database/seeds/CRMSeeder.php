<?php

use REW\Phinx\Seed\AbstractSeed;
use REW\Seed\Faker\CRM\CampaignFaker;
use REW\Seed\Faker\CRM\GroupFaker;
use REW\Seed\Faker\CRM\LeadFaker;

// TODO: fake campaign emails
// TODO: fake action plans
// TODO: fake form letters
// TODO: fake uploader files
// TODO: fake auto responders
// TODO: fake calendar events
// TODO: fake social connect data

/**
 * CRMSeeder
 * - 10 campaigns
 * - 25 groups
 * - 1,000 leads
 */
class CRMSeeder extends AbstractSeed
{

    /**
     * Campaign table
     * @var string
     */
    const TABLE_CAMPAIGNS = 'campaigns';

    /**
     * Group table
     * @var string
     */
    const TABLE_GROUPS = 'groups';

    /**
     * Lead table
     * @var string
     */
    const TABLE_LEADS = 'users';

    /**
     * Number of leads to create during a single run
     * @var integer
     */
    const LEADS_TO_CREATE = 1000;

    /**
     * Fake CRM data
     * @return void
     */
    public function run()
    {

        // Fake groups
        $groupIds = [];
        $groupFaker = new GroupFaker;
        $groupTable = self::TABLE_GROUPS;
        $groups = $groupFaker->generate(25);
        foreach ($groups as $group) {
            $this->insert($groupTable, $group);
            $groupIds[] = $this->lastInsertId();
        }

        // Fake campaigns
        $campaignFaker = new CampaignFaker;
        $campaignTable = self::TABLE_CAMPAIGNS;
        $campaigns = $campaignFaker->generate(10);
        foreach ($campaigns as $campaign) {
            // Insert fake campaign to database
            $this->insert($campaignTable, $campaign);
            $campaignId = $this->lastInsertId();

            // Randomly assign groups
            if (mt_rand(1, 2) === 1) {
                $assignGroups = array_rand($groupIds, mt_rand(1, count($groupIds) / 2));
                $assignGroups = is_array($assignGroups) ? $assignGroups : [$assignGroups];
                foreach ($assignGroups as $groupId) {
                    $this->insert('campaigns_groups', [
                        'group_id' => $groupIds[$groupId],
                        'campaign_id' => $campaignId
                    ]);
                }
            }
        }

        // Generate some fake leads
        $leadFaker = new LeadFaker;
        $leadTable = self::TABLE_LEADS;

        $totalAttempts = 0;
        $leadsLeft = self::LEADS_TO_CREATE;
        while ($leadsLeft > 0) {
            $leadsCreated = $failedCreations = 0;
            $leads = $leadFaker->generate(1000);
            foreach ($leads as $lead) {
                // Total Leads Created
                if ($leadsCreated >= $leadsLeft) {
                    break;
                }

                // Increment Total Leads
                $totalAttempts++;

                // Insert fake lead to database
                try {
                    $this->insert($leadTable, $lead);
                    $leadId = $this->lastInsertId();

                    // Randomly assign groups
                    if (mt_rand(1, 2) === 1) {
                        $assignGroups = array_rand($groupIds, mt_rand(1, count($groupIds) / 2));
                        $assignGroups = is_array($assignGroups) ? $assignGroups : [$assignGroups];
                        foreach ($assignGroups as $groupId) {
                            $this->insert('users_groups', [
                                'group_id' => $groupIds[$groupId],
                                'user_id' => $leadId
                            ]);
                        }
                    }

                    //Increment Leads Created
                    $leadsCreated++;
                } catch (\Exception $e) {
                    $failedCreations++;
                }

                // TODO: assign lead to agent
                // TODO: assign lead to lender
            }
            unset($leads);
            $leadsLeft = $leadsLeft - $leadsCreated;
            echo $leadsCreated . " leads created this run." . PHP_EOL;
            echo $failedCreations. " failed creations due to SQL errors." . PHP_EOL;
            echo $leadsLeft . " leads left to create." . PHP_EOL . PHP_EOL;

            if ($totalAttempts > 2 * self::LEADS_TO_CREATE) {
                echo "Total attempts have crossed the threshold of ".($totalAttempts > 2*self::LEADS_TO_CREATE).".  Terminating seeder." . PHP_EOL;
                break;
            }
        }
    }
}
