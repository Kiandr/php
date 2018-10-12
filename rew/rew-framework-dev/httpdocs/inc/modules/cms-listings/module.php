<?php

// Only Display Agent's CMS Listings On Agent Subdomain
if (isset(Settings::getInstance()->SETTINGS['agent']) && Settings::getInstance()->SETTINGS['agent'] !== 1) {
    $_REQUEST['agent_id'] = Settings::getInstance()->SETTINGS['agent'];
}
