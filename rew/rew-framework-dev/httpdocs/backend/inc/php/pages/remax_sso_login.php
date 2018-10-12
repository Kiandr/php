<?php

// RE/MAX Integra's Launchpad SSO System
if (!empty(Settings::getInstance()->MODULES['REW_REMAX_LAUNCHPAD'])
&& (!empty($_POST['remax_sso_validate']) || (!empty($_GET['remax_username']) && !empty($_GET['remax_token'])))) {
    // DB Connection
    $db = DB::get();

    // Central SSO Site's Initial Username Verification Request
    if (!empty($_POST['remax_sso_validate'])) {
        $json = array();

        // Check if Requested RE/MAX Username Matches Any Agent Accounts
        $result = $db->prepare("SELECT `id` FROM `" . LM_TABLE_AGENTS . "` WHERE `remax_launchpad_username` = :username;");
        $result->execute(array('username' => $_POST['remax_sso_validate']));
        $remax_user = $result->fetch();

        // Username Had a Match
        if (!empty($remax_user['id'])) {
            $json['agent'] = $remax_user['id'];
        }

        // Return JSON Response
        header('Content-Type: application/json');
        die(json_encode($json));
    }

    // User is Being Redirected to This Backend for Auto-login
    if (!empty($_GET['remax_username']) && !empty($_GET['remax_token'])) {
        $token_verified = false;

        // Confirm Token is Valid
        Util_Curl::setBaseURL('https://sso.realestatewebmasters.com');
        $response = Util_Curl::executeRequest('/launchpad/accounts/verify_token.php', array('remax_username' => $_GET['remax_username'], 'remax_token' => $_GET['remax_token']), Util_Curl::REQUEST_TYPE_POST, array(
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true
        ));
        if (!empty($response)) {
            $json = json_decode($response);
            $token_verified = ($json->response == 'success') ? true : false;
        }

        if ($token_verified) {
            // Check if Requested RE/MAX Username Matches Any Agent Accounts
            $result = $db->prepare("SELECT `au`.`username`, `au`.`password` FROM `auth` `au` LEFT JOIN `" . LM_TABLE_AGENTS . "` `a` ON `a`.`auth` = `au`.`id` WHERE `a`.`remax_launchpad_username` = :remax_username;");
            $result->execute(array('remax_username' => $_GET['remax_username']));
            $remax_agent = $result->fetch();

            if (!empty($remax_agent['username']) && !empty($remax_agent['password'])) {
                // Validate User
                $authuser->authenticate($remax_agent['username'], $remax_agent['password'], $db);
                $authuser->validate($db);

                // Validate User
                if ($authuser->isValid()) {
                    // Log Event: Agent Logged In
                    $event = new History_Event_Action_Login(array(
                        'ip' => $_SERVER['REMOTE_ADDR'],
                        'via' => 'remax_sso'
                    ), array(
                        $authuser->getHistoryUser()
                    ));

                    // Save to DB
                    $event->save();

                    // Redirect
                    header('Location: ' . Settings::getInstance()->URLS['URL_BACKEND']);
                    exit;

                //Validation Failed
                } else {
                    throw new Exception_ValidationError('Incorrect username or password! Please try again.');
                }
            }
        }
    }
}
