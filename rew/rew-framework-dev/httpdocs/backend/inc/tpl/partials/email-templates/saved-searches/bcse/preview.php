<?php
/**
 * Saved Searches email template - preview
 */

// App DB
$db = DB::get();

// Load preview config file
$config = json_decode(file_get_contents(__DIR__ . "/preview.json"), true);

$agent = $db->fetch("SELECT * FROM agents ORDER BY RAND() LIMIT 1;");

$social_media = [];
if(!empty($params["social_media"]["from"])) {
    $social_media_agent = Backend_Agent::load($params["social_media"]["from"] == "agent" ? $agent["id"] : 1);
    $social_media = $social_media_agent->getSocialNetworks();
}

//Get Mailling address
if (!empty($params["mailing_address"]["from"])) {
    switch ($params["mailing_address"]["from"]) {
        case "agent":
            $office_id = $agent["office"];
            break;
        case "admin":
            $result = $db->fetch("SELECT office
                                                  FROM `agents`
                                                  WHERE id = :id;", ["id" => 1]);
            $office_id = $result["office"];
            break;
        default:
            $office_id = null;
    }

    // Use default office
    if (empty($office_id)) {
        $office_id = $params["mailing_address"]["office_id"];
    }

    if (!empty($office_id)) {
        $office = $db->fetch("SELECT title, address, city, state, zip
                                              FROM `featured_offices`
                                              WHERE id = :id;", ["id" => $office_id]);
    }
}


echo $this->render(__DIR__ . '/index.php', [
    "search" => $config["search"],
    "permalink" => "permalink",
    "site" => [
        "url" =>  Settings::getInstance()->SETTINGS['URL'],
        "name" => $_SERVER['HTTP_HOST'],
    ],
    'user' => $config["user"],
    "listings" => array_splice($config["listings"], 0, $params["listings"]["num_rows"]*2),
    'agent' => $agent,
    'social_media' => $social_media,
    "office" => $office,
    "unsubscribe" => "unsub_url",
    "sub_preferences" => "sub_url",
    "message" => $message ?: $config["default_message"],
    "params" => $params,
]);