<?php

// Agent data
$agent = $this->config('agent');
if (is_numeric($agent)) {
    $db = DB::get();
    $query = $db->prepare("SELECT CONCAT(`first_name`, ' ', `last_name`) AS `name`, `image`, `remarks`, `cell_phone`, `office_phone` FROM `agents` WHERE `id` = :id LIMIT 1;");
    $query->execute(array('id' => $agent));
    if ($agent = $query->fetch()) {
        // Background image
        $container = $this->getContainer();
        $page = $container->getPage();
        $bg = $page->info('feature_image');
        if (!empty($bg)) {
            if (file_Exists(DIR_FEATURED_IMAGES . $bg)) {
                $bg = URL_FEATURED_IMAGES . $bg;
                if (!Skin::hasFeature(Skin::PRE_BUILT_ASSETS)) {
                    $page->addStylesheet('@agent-background-path: url(' . $bg . ');');
                } else {
                    $bg = null;
                }
            } else {
                $bg = null;
            }
        }
    }
}
