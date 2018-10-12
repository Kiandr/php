<?php

// Check CMS settings authorization
$container = \Container::getInstance();
$skin = $container->get(REW\Core\Interfaces\SkinInterface::class);
$settingsAuth = $container->make(REW\Backend\Auth\SettingsAuth::class);
if (!$settingsAuth->canManageCmsSettings($authuser, $skin)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to view content settings'
    );
}

// Success
$success = array();

// Errors
$errors = array();

// DB Connection
$db = DB::get();

// Skin Directory
$skin = isset($skin->name) ? $skin->name : Skin::getDirectory();

// Skin's class name
$skin_class = Skin::getClass($skin);

// Skin's settings key
$skin_class_exists = class_exists($skin_class);
if ($skin_class_exists) {
    $reflectSkin = new ReflectionClass($skin_class);
    if ($reflectSkin->hasConstant(SETTINGS_KEY)) {
        $settings_key = $skin_class::SETTINGS_KEY;
    }
}

// Logos Upload Notes and Warnings
define(UPLOAD_WARNING_LOGO, '<strong>Warning</strong>. Uploading a new logo will overwrite and replace the existing logo. ' .
    'Replaced logos cannot be recovered.');
define(UPLOAD_NOTE_LOGO, '<strong>Note</strong>. Changing your logo may affect your compliance with your MLS Board. ' .
    'It is your responsibility to know the Compliance rules and regulations stipulated by your MLS Board(s) (and brokerage) ' .
    'and to ensure your site meets these requirements.');
define(UPLOAD_RETINA_NOTE_LOGO, '<strong>Retina Upload Option</strong>. This option is to accommodate Apple retina displays ' .
    'which have a high pixel density. These images are high resolution. ' .
    '<span class=".text text--negative">You must first upload and save a regular display logo before being able to upload the ' .
    'corresponding retina display logo.</span>');
// Logos Upload Type Constant
define(UPLOAD_TYPE_LOGO, 'cms_settings_logo');
// Logo Constants
define(LOGO_HOMEPAGE, 'logo_homepage');
define(LOGO_HOMEPAGE_TITLE, 'Cover Page Logo');
define(LOGO_HOMEPAGE_HINT, 'This will update the logo in the header for all pages using the cover page template.');
define(LOGO_CONTENT_PAGE, 'logo_content_page');
define(LOGO_CONTENT_PAGE_TITLE, 'Content Page Logo');
define(LOGO_CONTENT_PAGE_HINT, 'This will update the logo in the header for all pages not using the cover page template.');
define(LOGO_FOOTER, 'logo_footer');
define(LOGO_FOOTER_TITLE, 'Footer Logo');
define(LOGO_FOOTER_HINT, 'This will update the logo in the page footer.');
define(LOGO_RETINA, 'retina_');
define(LOGO_FOR_RETINA, ' for Retina');

// Logo Sizes and Use Uploader
$logo_sizes = [];
$use_uploader = [
  'homepage' => true,
  'content_page' => true,
  'footer' => true
];
switch ($skin) {
    case 'bcse':
        $logo_sizes['homepage'] = '600px X 130px';
        $logo_sizes['content_page'] = '600px X 130px';
        $logo_sizes['footer'] = '231px X 50px';
        $use_uploader['homepage'] = false;
        break;
    case 'ce':
        $logo_sizes['homepage'] = '190px X 20px';
        $logo_sizes['content_page'] = '190px X 20px';
        $logo_sizes['footer'] = '231px X 50px';
        break;
    case 'elite':
        $logo_sizes['homepage'] = '176px X 43px';
        $logo_sizes['content_page'] = '176px X 43px';
        $logo_sizes['footer'] = '300px X 73px';
        break;
    case 'fese':
        $logo_sizes['homepage'] = '190px X 20px';
        $logo_sizes['content_page'] = '190px X 20px';
        $logo_sizes['footer'] = '190px X 20px';
        $use_uploader['footer'] = false;
        break;
    case 'lec-2013':
        $logo_sizes['homepage'] = '600px X 130px';
        $logo_sizes['content_page'] = '600px X 130px';
        $logo_sizes['footer'] = '356px X 74px';
        $use_uploader['footer'] = false;
        break;
    case 'lec-2015':
        $logo_sizes['homepage'] = '655px X 149px';
        $logo_sizes['content_page'] = '655px X 149px';
        $logo_sizes['footer'] = '220px X 50px';
        $use_uploader['footer'] = false;
        break;
    case 'discover':
        $logo_sizes['homepage'] = '214px X 30px';
        $logo_sizes['content_page'] = '214px X 30px';
        $logo_sizes['footer'] = '214px X 30px';
        $logo_sizes['extra'] = '<br><p>Maximum height: 54px - Note: Logos larger than the maximum height will be cut off and will not appear correctly</p>';
        $use_uploader['footer'] = false;
        break;
    default:
        $logo_sizes['homepage'] = '168px X 30px';
        $logo_sizes['content_page'] = '168px X 30px';
        $logo_sizes['footer'] = '168px X 30px';
        $use_uploader['footer'] = false;
        break;
}
// Logo Settings
$logo_size_opening_text = 'Recommended Size: ';
$logo_upload_field_ext = '_upload';
$logoSettings = [
    LOGO_HOMEPAGE => [
        'title' => LOGO_HOMEPAGE_TITLE,
        'upload_field' => LOGO_HOMEPAGE . $logo_upload_field_ext,
        'upload_field_retina' => LOGO_RETINA . LOGO_HOMEPAGE . $logo_upload_field_ext,
        'recommended_size' => $logo_size_opening_text . $logo_sizes['homepage'] . $logo_sizes['extra'],
        'use_uploader' => $use_uploader['homepage'],
        'hint' => LOGO_HOMEPAGE_HINT
    ],
    LOGO_CONTENT_PAGE => [
        'title' => LOGO_CONTENT_PAGE_TITLE,
        'upload_field' => LOGO_CONTENT_PAGE . $logo_upload_field_ext,
        'upload_field_retina' => LOGO_RETINA . LOGO_CONTENT_PAGE . $logo_upload_field_ext,
        'recommended_size' => $logo_size_opening_text . $logo_sizes['content_page'] . $logo_sizes['extra'],
        'use_uploader' => $use_uploader['content_page'],
        'hint' => LOGO_CONTENT_PAGE_HINT
    ],
    LOGO_FOOTER => [
        'title' => LOGO_FOOTER_TITLE,
        'upload_field' => LOGO_FOOTER . $logo_upload_field_ext,
        'upload_field_retina' => LOGO_RETINA . LOGO_FOOTER . $logo_upload_field_ext,
        'recommended_size' => $logo_size_opening_text . $logo_sizes['footer'] . $logo_sizes['extra'],
        'use_uploader' => $use_uploader['footer'],
        'hint' => LOGO_FOOTER_HINT
    ]
];

$settings = Settings::getInstance();

$settings['TABLES']['UPLOADS'];

// REW Legacy uploaded favicon
$legacy_favicon = sprintf('%sfavicon.ico', $_SERVER['DOCUMENT_ROOT']);

// Get current CMS settings
try {
    $query = sprintf(
        "SELECT * FROM `%s` WHERE `type` = 'favicon' LIMIT 1;",
        $settings['TABLES']['UPLOADS']
    );
    $result = $db->fetch($query);
    if (!empty($result['file'])) {
        $uploaded_favicon = $result['file'];
        $favicon = $result['file'];
    }
} catch (\PDOException $e) {
    $errors[] = __('Favicon could not be loaded.');
}

// Favicon Queries
$delete_favicon_query = sprintf(
    "DELETE FROM `%s` WHERE `file` = '%s' AND `type` = 'favicon';",
    $settings['TABLES']['UPLOADS'],
    $uploaded_favicon
);

if (isset($_GET['deletePhoto'])) {
    if (isset($_GET[LOGO_HOMEPAGE])) {
        deleteLogo($_GET['logoFile'], LOGO_HOMEPAGE_TITLE, $db, $success, $errors);
    } else if (isset($_GET[LOGO_RETINA . LOGO_HOMEPAGE])) {
        deleteLogo($_GET['logoFile'], LOGO_HOMEPAGE_TITLE . LOGO_FOR_RETINA, $db, $success, $errors);
    } else if (isset($_GET[LOGO_CONTENT_PAGE])) {
        deleteLogo($_GET['logoFile'], LOGO_CONTENT_PAGE_TITLE, $db, $success, $errors);
    } else if (isset($_GET[LOGO_RETINA . LOGO_CONTENT_PAGE])) {
        deleteLogo($_GET['logoFile'], LOGO_CONTENT_PAGE_TITLE . LOGO_FOR_RETINA, $db, $success, $errors);
    } else if (isset($_GET[LOGO_FOOTER])) {
        deleteLogo($_GET['logoFile'], LOGO_FOOTER_TITLE, $db, $success, $errors);
    } else if (isset($_GET[LOGO_RETINA . LOGO_FOOTER])) {
        deleteLogo($_GET['logoFile'], LOGO_FOOTER_TITLE . LOGO_FOR_RETINA, $db, $success, $errors);
    } else if(isset($_GET['favicon'])) {
        try {
            if ($db->query($delete_favicon_query)) {
                if (file_exists($uploaded_favicon)) {
                    unlink($uploaded_favicon);
                    $favicon = '';
                }
                $success[] = __('Favicon has successfully been removed.');
                unset($_POST['favicon']);
            } else {
                $errors[] = __('Favicon could not be removed.');
            }
        } catch (PDOException $exception) {
            $errors[] = __('Favicon could not be removed.');
        }
    }
    $authuser->setNotices($success, $errors);
    header('Location: /backend/settings/cms/');
    exit;
}

// Process POST Request
if (isset($_GET['submit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $query_extras = '';

    // Save and Update favicon image
    if ($_FILES['favicon_photo']['size'] > 0) {
        try {
            // Clear previous image
            if ($db->query($delete_favicon_query) && !empty($uploaded_favicon)) {
                $path = $settings['DIRS']['UPLOADS'] . $uploaded_favicon;
                if (file_exists($path)) {
                    unlink($path);
                }
                $favicon = '';
            }

            // Get File Uploader
            $uploader = new Backend_Uploader_Form('favicon_photo', 'images', 'favicon');
            // Bust Favicon cache
            $uploader->setName(time() . '-' . $uploader->getName());
            $uploader->handleUpload($settings['DIRS']['UPLOADS'], false);

            // Save site CMS Settings
            $insert = "INSERT INTO `%1\$s` (`file`, `type`) VALUES (IFNULL(:file, `file`),  'favicon');";
            $update = "UPDATE `%1\$s` SET `file` = IFNULL(:file, `file`) WHERE `type` = 'favicon';";

            $query = (empty($favicon)) ? $insert : $update;
            $cms_settings = $db->prepare(sprintf($query, $settings['TABLES']['UPLOADS']));
            $cms_settings->execute([
                'file' => $uploader->getName()
            ]);

        } catch (\PDOException $e) {
            $errors[] = __("Error while trying to save site CMS settings!");
        } catch (Exception $e) {
            $errors[] = __("Could not upload the favicon. " . $e->getMessage());
        }
    }

    // Logo Uploaders
    foreach ($logoSettings as $logoConstant => $logoData) {
        uploadInsertUpdateLogo($_POST[$logoConstant], $logoData['upload_field'], $logoConstant, $logoData['title'], $db, $success, $errors);
        uploadInsertUpdateLogo($_POST[LOGO_RETINA . $logoConstant], $logoData['upload_field_retina'], LOGO_RETINA . $logoConstant, $logoData['title'] . LOGO_FOR_RETINA, $db, $success, $errors);
    }

    // LEC 2015 Settings
    if ($skin === 'lec-2015') {

        // Available options
        $mortgage_terms = array(5, 10, 15, 20, 25, 30, 35);
        $interest_rates = array(3, 3.25, 3.5, 3.75, 4, 4.25, 4.5, 4.75, 5);

        try {
            // New skin settings from $_POST data
            $settings = $_POST['setting'][$settings_key];

            // Updated settings
            $data = array();

            // Mortgage calculator settings
            $data['down_percent']   = (int) $settings['down_percent'];
            $data['mortgage_term']  = (int) $settings['mortgage_term'];
            $data['interest_rate']  = (float) $settings['interest_rate'];

            // Must be greater than 0
            if (empty($data['down_percent'])) {
                throw new UnexpectedValueException('Down percent must be greater than 0');
            } elseif ($data['down_percent'] >= 100) {
                throw new UnexpectedValueException('Down percent must be less than 100%');
            }

            // Must be greater than 0
            if (empty($data['mortgage_term'])) {
                throw new UnexpectedValueException('Mortgage term must be greater than 0');
            } elseif (!in_array($data['mortgage_term'], $mortgage_terms)) {
                throw new UnexpectedValueException('Please select a valid mortgage term');
            }

            // Must be greater than 0
            if (empty($data['interest_rate'])) {
                throw new UnexpectedValueException('Interest rate must be greater than 0');
            } elseif (!in_array($data['interest_rate'], $interest_rates)) {
                throw new UnexpectedValueException('Please select a valid interest rate');
            }

            // Save new settings
            Settings::set($settings_key, json_encode($data));

            // Success, redirect back to form
            $success[] = 'Website Settings have successfully been saved.';
            $authuser->setNotices($success, $errors);
            header('Location: ?success');
            exit;

            // Validation error
        } catch (UnexpectedValueException $e) {
            $errors[] = $e->getMessage();

            // Database error
        } catch (PDOException $e) {
            $errors[] = 'Website Settings could not be saved, please try again.';
            //$errors[] = $e->getMessage();
        }

    // "Barbara Corcoran Special Edition"
    } elseif (Skin::hasFeature(Skin::AGENT_SPOTLIGHT)) {

        try {
            // New skin settings from $_POST data
            $settings = $_POST['setting'][$settings_key];

            // Updated settings
            $data = array();

            // Agent spotlight settings
            if (!empty(Settings::getInstance()->MODULES['REW_AGENT_SPOTLIGHT'])) {
                $data['agent_id']      = (is_numeric($settings['agent_id']) ? (int) $settings['agent_id'] : ($settings['agent_id'] === 'RAND' ? $settings['agent_id'] : false));
                $data['agent_phone']   = !empty($settings['agent_phone']);
                $data['agent_cell']    = !empty($settings['agent_cell']);
            }

            // Search result settings
            $data['more_options']  = !empty($settings['more_options']);

            // Save new settings
            Settings::set($settings_key, json_encode($data));

            // Success, redirect back to form
            $success[] = 'Website Settings have successfully been saved.';
            $authuser->setNotices($success, $errors);
            header('Location: ?success');
            exit;

            // Error occurred
        } catch (Exception $e) {
            $errors[] = 'Website Settings could not be saved, please try again.';
            //$errors[] = $e->getMessage();
        }

    }

    if(empty($errors)) {
        $success[] = __(' Website Settings have successfully been saved.');
    }

}

// Get updated CMS settings
try {
    $query = sprintf(
        "SELECT * FROM `%s` WHERE `type` = 'favicon';",
        $settings['TABLES']['UPLOADS']
    );
    $result = $db->fetch($query);
    if (!empty($result['file'])) {
        $favicon = $result['file'];
    }
} catch (\PDOException $e) {
    $errors[] = __('Favicon could not be loaded.');
}

if (Skin::hasFeature(Skin::AGENT_SPOTLIGHT)) {
    // Available Agents
    $agents = array();
    if (!empty(Settings::getInstance()->MODULES['REW_AGENT_SPOTLIGHT'])) {
        $result = $db->query("SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS `name` FROM `agents` WHERE `display_feature` = 'Y' ORDER BY `last_name` ASC, `first_name` ASC;");
        while ($agent = $result->fetch()) {
            $agents[] = $agent;
        }
    }
}

// Get skin's settings
if ($skin_class_exists) {
    if ($reflectSkin->hasMethod('getSettings')) {
        $settings = $skin_class::getSettings();
    }
}
// Get Existing Logos
foreach ($logoSettings as $logoConstant => $logoData) {
    ${$logoConstant} = getLogo($logoConstant, $logoData['title'], $db, $errors);
    ${LOGO_RETINA . $logoConstant} = getLogo(LOGO_RETINA . $logoConstant, $logoData['title'] . LOGO_FOR_RETINA, $db, $errors);
}

// Get Logo, return string of file name or null
function getLogo($logo_constant, $logo_title, $db, &$errors)
{
    try {
        $query = sprintf("SELECT `file` FROM `%s` WHERE `file` LIKE '%s%%' AND `type` = '%s' LIMIT 1;", CMS_TABLE_UPLOADS, $logo_constant, UPLOAD_TYPE_LOGO);
        $result = $db->fetch($query);
        if (!empty($result['file'])) {
            return $result['file'];
        }
    } catch (\PDOException $e) {
        $errors[] = __($logo_title . ' could not be loaded. ');
    }
    return null;
}

// Delete a Logo
function deleteLogo($file_name, $logo_title, $db, &$success, &$errors) {
    if (empty($file_name)) {
        $errors[] = __($logo_title . ' could not be removed. Missing file name.');
        return;
    }
    $query = sprintf("DELETE FROM `%s` WHERE `file` = '%s' AND `type` = '%s';", CMS_TABLE_UPLOADS, $file_name, UPLOAD_TYPE_LOGO);
    try {
        if ($db->query($query)) {
            $uploaded_logo = sprintf('%s'.$file_name, DIR_UPLOADS);
            if (file_exists($uploaded_logo)) {
                unlink($uploaded_logo);
            }
            $success[] = __($logo_title . ' has successfully been removed.');
        } else {
            $errors[] = __($logo_title . ' could not be removed.');
        }
    } catch (\PDOException $e) {
        $errors[] = __($logo_title . ' could not be removed.');
    }
}

// Upload, Insert or Update a Logo
function uploadInsertUpdateLogo($file_name, $logo_upload_field, $logo_constant, $logo_title, $db, &$success, &$errors) {

    // leave if there was no upload
    if (empty($_FILES[$logo_upload_field]['size'] > 0)) return;
    // delete existing logo
    if (!empty($file_name)) {
        $uploaded_logo = sprintf('%s' . $file_name, DIR_UPLOADS);
        if (file_exists($uploaded_logo)) {
            unlink($uploaded_logo);
            $success[] = __('Existing logo has successfully been removed. ');
        }
    }
    // process upload
    try {
        // Get File Uploader
        $uploader = new Backend_Uploader_Form($logo_upload_field);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif']);
        // adding random number to filename to avoid caching
        $uploader->setName($logo_constant . '.' . rand(10, 99) . '.' . pathinfo($uploader->getName(), PATHINFO_EXTENSION));
        $uploader->handleUpload(DIR_UPLOADS, false);
    } catch (Exception $e) {
        $errors[] = __("Could not upload the " . $logo_title . ". ");
    }

    // insert or update database record
    $insert = "INSERT INTO `%1\$s` (`file`, `type`) VALUES (IFNULL(:file, `file`),  '%2\$s');";
    $update = "UPDATE `%1\$s` SET `file` = IFNULL(:file, `file`), `type` = '%2\$s' WHERE `file` LIKE '%3\$s';";
    $action_type = empty($file_name) ? 'Saved' : 'Updated';
    try {
        $query = (empty($file_name)) ? $insert : $update;
        $cms_settings = $db->prepare(sprintf(
            $query,
            CMS_TABLE_UPLOADS,
            UPLOAD_TYPE_LOGO,
            $file_name
        ));
        $cms_settings->execute([
            'file' => $uploader->getName()
        ]);
        $success[] = __($logo_title . " " . $action_type . ". ");
    } catch(\PDOException $e) {
        $errors[] = __("Error while trying to save " . $logo_title . ". ");
    }
}
