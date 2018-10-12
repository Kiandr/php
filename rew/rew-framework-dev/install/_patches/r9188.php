<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// Require RATE module to be installed
if (!empty(Settings::getInstance()->MODULES['REW_RADIO_LANDING_PAGE'])) {

	// DB connection
	$db = DB::get();

	// Update RATE audio files to new format
	$query = $db->prepare("SELECT `value` FROM `landing_pods_fields` WHERE `pod_name` = :pod_name AND `name` = :name LIMIT 1;");
	$query->execute(array('pod_name' => 'ad-player', 'name' => 'audio-files'));
	if ($audio = $query->fetchColumn()) {
		$audio = unserialize($audio);
		if (is_array($audio)) {
			foreach ($audio as $key => $ad) {
				if (is_array($ad)) continue;
				$audio[$key] = array('title' => $ad);
			}
		}
	}

	// Save changes
	if (!empty($audio)) {
		$query = $db->prepare("UPDATE `landing_pods_fields` SET `value` = :value WHERE `pod_name` = :pod_name AND `name` = :name;");
		$query->execute(array('pod_name' => 'ad-player', 'name' => 'audio-files', 'value' => serialize($audio)));
	}

}