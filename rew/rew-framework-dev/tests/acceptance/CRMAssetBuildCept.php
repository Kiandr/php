<?php

use REW\Backend\Asset\Loader;
use REW\Test\AcceptanceTester;

$I = new AcceptanceTester($scenario);
$I->wantTo('verify CRM assets have been built');

// Verify build folder exists
$I->amInPath('httpdocs/backend/build');

// Verify CSS manifest file exists
$I->seeFileFound('assets.json', 'css');
$I->seeInThisFile('"app.css"');

// Verify JS manifest files exists
$I->seeFileFound('webpack.json', 'js');
$I->seeFileFound('assets.json', 'js');
$I->seeInThisFile('"manifest.js"');
$I->seeInThisFile('"bundle.js"');
$I->seeInThisFile('"vendor.js"');
