<?php
namespace REW\Test\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use REW\Test\AcceptanceTester;
use REW\Test\Page\Backend\Util;

class E2E extends Config
{
    /**
     * @var array
     */
    protected $requiredFields = ['domainPrefix','domain'];
    public static $initialized;

    public function _beforeSuite($settings = array())
    {
        $this->initialized = true;
    }

    public function _before(\Codeception\TestInterface $test)
    {
        if (!$this->initialized) {
            $this->scenario = $test->getScenario();
            $this->I = new AcceptanceTester($this->scenario);
            $util = new Util($this->I);
            $util->setup();
            $this->initialized = true;
        }
    }
}
