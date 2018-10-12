<?php

// Listing MLS #
$mls_num = (!empty($this->config['mls_number'])) ? $this->config['mls_number'] : false;

// Track Whether to Output
$display = false;

// User Object
$user = Auth::get();

// Showing Suite Calendar
if (isset(Settings::getInstance()->MODULES['REW_SHOWING_SUITE']) && !empty(Settings::getInstance()->MODULES['REW_SHOWING_SUITE'])
&& !empty($mls_num)) {
    // Build PDO Object
    $db = DB::get('cms');

    // Check if User has an Agent
    if (!empty($user) && $user->isValid()) {
        $user_agent = $user->info('agent');
    }
    $user_agent = !empty($user_agent) ? $user_agent : '1';

    // Fetch Agent's Email
    $query = $db->prepare("SELECT `email`, `showing_suite_email` FROM `" . TABLE_AGENTS . "` WHERE `id` = :agent_id LIMIT 1;");
    $query->execute(array('agent_id' => $user_agent));
    $result = $query->fetch();
    $ss_email = (!empty($result['showing_suite_email']) ? $result['showing_suite_email'] : $result['email']);

    // Check if the Agent's ShowingSuite Account Contains the Current Listing
    if (!empty($ss_email)) {
        Util_Curl::setBaseURL('https://new.showingsuite.com/listings/check-mls');
        $response = Util_Curl::executeRequest('/clientid/' . urlencode($ss_email) . '/mls_number/' . urlencode($mls_num) . '/');
        if (stripos($response, 'listing found') !== false) {
            // Track Whether to Output
            $display = true;
        }
    }
}
