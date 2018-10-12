<?php

// Team data
$team = $this->config('team');
if (is_numeric($team)) {
    if ($team = Backend_Team::load($team)) {
        // Get Team Primary Agent
        $primaryAgent = Backend_Agent:: load($team->getPrimaryAgent());

        // Background image
        $container = $this->getContainer();
        $page = $container->getPage();
        $bg = $page->info('feature_image');
        if (!empty($bg)) {
            if (file_exists(DIR_FEATURED_IMAGES . $bg)) {
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
