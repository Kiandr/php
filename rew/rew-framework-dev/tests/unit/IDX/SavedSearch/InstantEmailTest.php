<?php

namespace REW\Test\IDX\SavedSearch;

use Mockery\Exception\NoMatchingExpectationException;
use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\IDX\ComplianceInterface as IDXComplianceInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use History_Event_Email_Listings;
use Mockery;

class InstantEmailTest extends \Codeception\Test\Unit
{

    /**
     * @var int
     */
    const LISTING_LIMIT = 5;

    /**
     * @var Mockery\MockInterface|PageInterface
     */
    private $page;

    /**
     * @var Mockery\MockInterface|SkinInterface
     */
    private $skin;

    /**
     * @var Mockery\MockInterface|DatabaseInterface
     */
    private $db;

    /**
     * @var Mockery\MockInterface|SettingsInterface
     */
    private $settings;

    /**
     * @var Mockery\MockInterface|IDXComplianceInterface
     */
    private $idxCompliance;

    /**
     * @var Mockery\MockInterface|FactoryInterface
     */
    private $viewFactory;

    /**
     * @var int
     */
    private $savedSearchId;

    /**
     * @var int
     */
    private $createdByAgentId;

    /**
     * @var array
     */
    private $savedSearchData = [];

    /**
     * @var object
     */
    private $agent;

    /**
     * @var array
     */
    private $siteUrls = [];

    /**
     * @var array
     */
    private $listingResults = [];

    /**
     * @var int
     */
    private $listingResultsCount;

    /**
     * @var string
     */
    private $listingsMarkup;

    /**
     * @var object
     */
    private $mailer;

    /**
     * @var array
     */
    private $tags;

    /**
     * @var array
     */
    private $successMessages = [];

    /**
     * @var array
     */
    private $errorMessages = [];

    /**
     * @return void
     */
    protected function _before () {
        $this->page = Mockery::mock(PageInterface::class);
        $this->skin = Mockery::mock(SkinInterface::class);
        $this->db = Mockery::mock(DBInterface::class);;
        $this->settings = Mockery::mock(SettingsInterface::class);
        $this->idxCompliance = Mockery::mock(IDXComplianceInterface::class);
        $this->viewFactory = Mockery::mock(FactoryInterface::class);
    }

    /**
     * @return void
     */
    protected function _after()
    {
        Mockery::close();
    }


    public function setDataProvider() {
        return [
            [1, 1],
            [1, null]
        ];
    }

    /**
     * @covers IDX_SavedSearch_InstantEmail::setData
     * @dataProvider  setDataProvider
     * @param $savedSearchId int
     * @param $createdByAgentId int|null
     */
    public function testSetData($savedSearchId, $createdByAgentId = null)
    {
        $this->assertInternalType("int", $savedSearchId);
        if (is_null($createdByAgentId)) {
            $this->assertNull($createdByAgentId);
        } else {
            $this->assertInternalType("int", $createdByAgentId);
        }
    }


    public function setSavedSearchDataProvider() {
        return [
            [1]
        ];
    }

    /**
     * @covers IDX_SavedSearch_InstantEmail::setSavedSearchData
     * @dataProvider  setSavedSearchDataProvider
     * @param $savedSearchId int
     */
    public function testSetSavedSearchData($savedSearchId)
    {
        $this->assertInternalType("int", $savedSearchId);
    }


    public function setAgentProvider() {
        return [
            [1]
        ];
    }

    /**
     * @covers IDX_SavedSearch_InstantEmail::setAgent
     * @dataProvider  setAgentProvider
     * @param $agentId int
     */
    public function testSetAgent($agentId)
    {
        $this->assertInternalType("int", $agentId);
    }


    public function setSiteUrlsProvider() {
        return [
            [Mockery::mock(Backend_Agent::class)]
        ];
    }

    /**
     * @covers IDX_SavedSearch_InstantEmail::setSiteUrls
     * @dataProvider  setSiteUrlsProvider
     * @param $agent object
     */
    public function testSetSiteUrls($agent)
    {
        $this->assertInternalType("object", $agent);
    }


    public function setListingsDataProvider() {
        return [
            [
                [],
                Mockery::mock(Backend_Agent::class),
                []
            ]
        ];
    }

    /**
     * @covers IDX_SavedSearch_InstantEmail::setListingsData
     * @dataProvider  setListingsDataProvider
     * @param search array
     * @param $agent object
     * @param $siteUrls array
     */
    public function testSetListingsData($search, $agent, $siteUrls)
    {
        $this->assertInternalType("array", $search);
        $this->assertInternalType("object", $agent);
        $this->assertInternalType("array", $siteUrls);
    }


    public function setListingsMarkupProvider() {
        return [
            [
                [],
                "",
            ]
        ];
    }

    /**
     * @covers IDX_SavedSearch_InstantEmail::setListingsMarkup
     * @dataProvider  setListingsMarkupProvider
     * @param $results array
     * @param $search_idx string
     */
    public function testSetListingsMarkup($results, $search_idx)
    {
        $this->assertInternalType("array", $results);
        $this->assertInternalType("string", $search_idx);
    }


    public function setEmailProvider() {
        return [
            [
                [],
                Mockery::mock(Backend_Agent::class),
                [],
                1
            ]
        ];
    }

    /**
     * @covers IDX_SavedSearch_InstantEmail::setEmail
     * @dataProvider  setEmailProvider
     * @param $savedSearchData array
     * @param $agent object
     * @param $siteUrls array
     * @param $listingResultsCount int
     */
    public function testSetEmail($savedSearchData, $agent, $siteUrls, $listingResultsCount)
    {
        $this->assertInternalType("array", $savedSearchData);
        $this->assertInternalType("object", $agent);
        $this->assertInternalType("array", $siteUrls);
        $this->assertInternalType("int", $listingResultsCount);
    }


    /**
     * @covers IDX_SavedSearch_InstantEmail::getSuccessMessages
     */
    public function testGetSuccessMessages()
    {
        $this->assertInternalType("array", $this->successMessages);
    }


    /**
     * @covers IDX_SavedSearch_InstantEmail::getErrorMessages
     */
    public function testGetErrorMessages()
    {
        $this->assertInternalType("array", $this->errorMessages);
    }


    /**
     * @covers IDX_SavedSearch_InstantEmail::sendEmail
     */
    public function testSendEmail()
    {
        $tags = $this->tags;

        $mailer = Mockery::mock(Backend_Mailer::class);

        $mailer->shouldReceive('Send')->with($tags);
    }


    /**
     * @covers IDX_SavedSearch_InstantEmail::logSentEmail
     */
    public function logSentEmail()
    {
        $mailer = Mockery::mock(Backend_Mailer::class);

        $history_event_param = [
            'subject'   => $mailer->shouldReceive('getSubject')->andReturn('Test Subject'),
            'message'   => $mailer->shouldReceive('getMessage')->andReturn('Test Message'),
            'tags'      => $mailer->shouldReceive('getTags')->andReturn([])
        ];

        $history_user_lead = Mockery::mock(History_User_Lead::class);

        $event = new History_Event_Email_Listings($history_event_param, $history_user_lead, $this->db);
    }
}
