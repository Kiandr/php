<?php

namespace REW\Test;

use REW\Test\ApiTester;

class APICest
{

    /**
     * @var string
     */
    const AUTH_HEADER = 'X-REW-API-Key';

    /**
     * Include authentication header
     * @param \REW\Test\ApiTester $I
     */
    public function _before(ApiTester $I)
    {
        $I->haveHttpHeader(
            self::AUTH_HEADER,
            $I->grabFromConfig('key')
        );
    }

    /**
     * Require JSON 200 Response
     * @param \REW\Test\ApiTester $I
     */
    public function _after(ApiTester $I)
    {
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    /**
     * @param \REW\Test\ApiTester $I
     */
    public function tryToPing(ApiTester $I)
    {
        $I->wantTo('ping api');
        $I->sendGET('/api/crm/v1/ping');
        $I->seeResponseMatchesJsonType([
             'ttfb' => 'float',
             'timestamp' => 'integer'
        ]);
    }

    /**
     * @param \REW\Test\ApiTester $I
     */
    public function tryToListAgents(ApiTester $I)
    {
        $I->wantTo('list all agents');
        $I->sendGET('/api/crm/v1/agents');
    }

    /**
     * @param \REW\Test\ApiTester $I
     */
    public function tryToListGroups(ApiTester $I)
    {
        $I->wantTo('list all groups');
        $I->sendGET('/api/crm/v1/groups');
    }
}
