<?php

use REW\Phinx\Seed\AbstractSeed;
use REW\Seed\Faker\Company\TeamFaker;
use REW\Seed\Faker\Company\AgentFaker;
use REW\Seed\Faker\Company\OfficeFaker;
use REW\Seed\Faker\Company\LenderFaker;
use REW\Seed\Faker\Company\AssociateFaker;
use REW\Seed\Faker\UploadFaker;

/**
 * CompanySeeder
 * - 10 teams
 * - 100 offices
 * - 100 lenders
 * - 100 associates
 * - 1,000 agents
 */
class CompanySeeder extends AbstractSeed
{

    /**
     * Auth table
     * @var string
     */
    const TABLE_AUTH = 'auth';

    /**
     * Teams table
     * @var string
     */
    const TABLE_TEAMS = 'teams';

    /**
     * Agents table
     * @var string
     */
    const TABLE_AGENTS = 'agents';

    /**
     * Associates table
     * @var string
     */
    const TABLE_ASSOCIATES = 'associates';

    /**
     * Lenders table
     * @var string
     */
    const TABLE_LENDERS = 'lenders';

    /**
     * Offices table
     * @var string
     */
    const TABLE_OFFICES = 'featured_offices';

    /**
     * Fake company data
     * @return void
     */
    public function run()
    {

        // Fake upload generator
        $uploadFaker = new UploadFaker();
        $uploadTable = $uploadFaker::UPLOAD_TABLE_NAME;

        // Fake offices
        $officeIds = [];
        $officeFaker = new OfficeFaker;
        $officeTable = self::TABLE_OFFICES;
        $offices = $officeFaker->generate(100);
        foreach ($offices as $office) {
            $this->insert($officeTable, $office);
            $officeIds[] = $this->lastInsertId();
        }

        // Fake agents
        $agentIds = [];
        $agentFaker = new AgentFaker;
        $agentTable = self::TABLE_AGENTS;
        $agents = $agentFaker->generate(1000);
        foreach ($agents as $agent) {
            $this->insertAccount('Agent', $agent);

            // Assign agent to office
            if (rand(1, 2) === 1) {
                $agent['office'] = $officeIds[
                    array_rand($officeIds)
                ];
            }

            // Set agent created timestamp
            $agent['timestamp'] = $agent['timestamp_created'];
            unset($agent['timestamp_created']);
            unset($agent['timestamp_updated']);

            // Insert agent record to database
            $this->insert($agentTable, $agent);
            $agentId = $this->lastInsertId();
            $agentIds[] = $agentId;
        }

        // Generate fake associates
        $associateFaker = new AssociateFaker;
        $associateTable = self::TABLE_ASSOCIATES;
        $associates = $associateFaker->generate(100);
        foreach ($associates as $associate) {
            $this->insertAccount('Associate', $associate);
            $this->insert($associateTable, $associate);

            // Insert fake associate image to uploads
            if ($associateFaker->getFaker()->boolean) {
                $uploads = $uploadFaker->generate(1, [
                    'row' => $this->lastInsertId(),
                    'type' => 'associate'
                ]);
                $this->insert($uploadTable, $uploads);
            }
        }

        // Generate fake lenders
        $lenderFaker = new LenderFaker;
        $lenderTable = self::TABLE_LENDERS;
        $lenders = $lenderFaker->generate(100);
        foreach ($lenders as $lender) {
            $this->insertAccount('Lender', $lender);
            $this->insert($lenderTable, $lender);

            // Insert fake lender image to uploads
            if ($lenderFaker->getFaker()->boolean) {
                $uploads = $uploadFaker->generate(1, [
                    'row' => $this->lastInsertId(),
                    'type' => 'lender'
                ]);
                $this->insert($uploadTable, $uploads);
            }
        }

        // Fake teams
        $teamFaker = new TeamFaker;
        $teamTable = self::TABLE_TEAMS;
        $teams = $teamFaker->generate(10);
        foreach ($teams as $team) {
            $team_lead = $agentIds[array_rand($agentIds)];

            // Insert team record into database
            $this->insert($teamTable, array_merge($team, [
                'agent_id' => $team_lead
            ]));

            // Assign agents to new team
            $teamId = $this->lastInsertId();
            $teamAgents = array_merge([$team_lead], array_rand($agentIds, mt_rand(1, 20)));
            foreach ($teamAgents as $agentId) {
                $this->insert('team_agents', [
                    'agent_id' => $agentIds[$agentId],
                    'team_id' => $teamId
                ]);
            }
        }
    }

    /**
     * @param string $type
     * @param array $account
     */
    protected function insertAccount($type, array &$account)
    {
        $authTable = self::TABLE_AUTH;

        // Insert auth record
        $this->insert($authTable, [
            'type' => $type,
            'username' => $account['username'],
            'password' => $account['password'],
            'timestamp_created' => $account['timestamp_created'],
            'timestamp_updated' => $account['timestamp_updated']
        ]);

        // Set auth id for new account record
        $account['auth'] = $this->lastInsertId();
        $unset = ['username', 'password'];
        foreach ($unset as $k) {
            unset($account[$k]);
        }
    }
}
