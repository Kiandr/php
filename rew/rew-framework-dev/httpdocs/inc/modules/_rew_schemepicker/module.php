<?php

// Start Session..
use REW\Core\Interfaces\SkinInterface;
use REW\Core\Interfaces\SkinProviderInterface;

@session_start();

// Config: Show All Skins
$show_all_skins = $this->config('show_all_skins') ? $this->config('show_all_skins') : false;

// Switch Skin & Scheme
if (!empty($_GET['skin-scheme'])) {
    list($_GET['skin'], $_GET['scheme']) = explode('/', $_GET['skin-scheme'], 2);
}

// Allow Skin Change
if (!empty($show_all_skins)) {
    // Select Skin
    if (!empty($_GET['skin'])) {
        $_SESSION['skin'] = $_GET['skin'];
    }
    if (!empty($_SESSION['skin'])) {
        Settings::getInstance()->SKIN = $_SESSION['skin'];
    }
}

// Select Scheme
if (!empty($_GET['scheme'])) {
    $_SESSION['scheme'] = $_GET['scheme'];
}
if (!empty($_SESSION['scheme'])) {
    Settings::getInstance()->SKIN_SCHEME = $_SESSION['scheme'];
}

// Path to Skins
$skins_dir = Settings::getInstance()->DIRS['SKINS'];

// Locate Skins
if (is_dir($skins_dir) && $handle = opendir($skins_dir)) {
    while (false !== ($file = readdir($handle))) {
        // Exclude BREW
        if (in_array($file, ['brew', 'default'])) {
            continue;
        }

        // Ignore Hidden
        if (substr($file, 0, 1) == "." || substr($file, 0, 1) == "_") {
            continue;
        }

        // Only Show Current Skin
        if (empty($show_all_skins) && $file != $this->settings->SKIN) {
            continue;
        }

        // Let's get the skin's name... Use a factory to get new objects so as not to change the application-wide
        // settings
        /** @var SkinProviderInterface $skinProvider */
        $skinProvider = $this->diContainer->get(SkinProviderInterface::class);

        /** @var SkinInterface $skinInstance */
        $skinInstance = $skinProvider->buildSkinInstance($file, 'default');

        $skinName = $skinInstance ? $skinInstance->getName() : ucwords(str_replace(array('-', '_'), array(' ', ' '), $file));

        // Skin Details
        $skin = array(
            'value' => $file,
            'title' => $skinName,
            'schemes' => array()
        );

        // Locate Schemes
        $schemes_dir = $skins_dir . $file . '/schemes';
        if (is_dir($schemes_dir) && ($shandle = opendir($schemes_dir))) {
            while (false !== ($scheme = readdir($shandle))) {
                // Ignore Hidden
                if (substr($scheme, 0, 1) == "." || substr($scheme, 0, 1) == "_") {
                    continue;
                }

                // Add Scheme
                $skin['schemes'][$scheme] = array('value' => $scheme, 'title' => ucwords(str_replace(array('-', '_'), array(' ', ' '), $scheme)));
            }

            // Close Dir
            closedir($shandle);

            // Sort Schemes
            ksort($skin['schemes']);

            // Force 'Default' as First Scheme
            if (isset($skin['schemes']['default'])) {
                $default = $skin['schemes']['default'];
                unset($skin['schemes']['default']);
                array_unshift($skin['schemes'], $default);
            }
        }

        // Add to Collection
        $skins[$file] = $skin;
    }

    // Close Dir
    closedir($handle);

    // Sort Skins
    ksort($skins);
}
