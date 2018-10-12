<?php

use REW\Theme\Enterprise\Installer;
use REW\Theme\Enterprise\Module;

return [
    Installer::INSTALLED_MODULES => [
        Module\DisableCoreModules\ModuleController::class,
        Module\DisableIdxViewOptions\ModuleController::class,
        Module\IdxPanelConstruct\ModuleController::class,
        Module\ValidateSnippetRename\ModuleController::class,
        REW\Module\EnhancedListings\ModuleController::class,
        Module\SinglePropertyWebsite\ModuleController::class
    ]
];
