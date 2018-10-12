<?php

/**
 * Saved Searches email template
 * @var array $search
 * @var string $permalink
 * @var array $site
 * @var array $user
 * @var array $listings
 * @var array $agent
 * @var array $social_media
 * @var array $office
 * @var string $unsubscribe
 * @var string $sub_preferences
 */


// Load config file
$globalConfig = json_decode(file_get_contents(__DIR__ . "/../config.json"), true);
$config = json_decode(file_get_contents(__DIR__ . "/config.json"), true);
$button = $config["style"]["buttons"][$config["config"]["button_corners"]];
$style = array_merge(array_diff_key($config["style"], ["buttons"]), $button);
$settings = $globalConfig["settings"];

echo $this->render(__DIR__ . "/index.tpl.php", [
    "style" => $style,
    "settings" => $settings,
    "search" => $search,
    "permalink" => $permalink,
    "site" => $site,
    'user' => $user,
    "listings" => $listings,
    'agent' => $agent,
    'social_media' => $social_media,
    "office" => $office,
    "unsubscribe" => $unsubscribe,
    "sub_preferences" => $sub_preferences,
    "message" => $message,
    "params" => $params
]);
