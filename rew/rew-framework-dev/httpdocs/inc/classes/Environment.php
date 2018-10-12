<?php

use REW\Core\Interfaces\EnvironmentInterface;
use \PHPMailer\RewMailer;

class Environment implements EnvironmentInterface
{
    /**
     * REW Core config
     * @var array
     */
    private $coreConfig = array();

    /**
     * Function to load the REW json config data
     *
     *  __          __     _____  _   _ _____ _   _  _____
     *  \ \        / /\   |  __ \| \ | |_   _| \ | |/ ____|
     *   \ \  /\  / /  \  | |__) |  \| | | | |  \| | |  __
     *    \ \/  \/ / /\ \ |  _  /| . ` | | | | . ` | | |_ |
     *     \  /\  / ____ \| | \ \| |\  |_| |_| |\  | |__| |
     *      \/  \/_/    \_\_|  \_\_| \_|_____|_| \_|\_____|
     *
     * DANGER WILL ROBINSON!
     *
     * This function also controls passwordless access to the backend.
     * If you are making changes BE SUPER DUPER SURE that you aren't letting
     * untrusted people access to the backend. Use BrowserStack to test!
     *
     * @return array
     */
    public function getCoreConfig()
    {
        if (empty($this->coreConfig)) {
            // Load the REW config
            if (file_exists($core_config_file = '/usr/share/rew/config.json')) {
                $this->coreConfig = json_decode(file_get_contents('/usr/share/rew/config.json'), true);
            } else {
                $this->coreConfig = array('office_ips' => array());
            }
        }

        return $this->coreConfig;
    }

    /**
     * Loads mail CRM settings.
     * @return EnvironmentInterface
     */
    public function loadMailCRMSettings()
    {
        $this->getCoreConfig();

        // Backend Mail settings
        $settings = RewMailer::GetCRMSettings();

        // Non-REW mail sender - don't obey whitelist settings
        if (!empty($settings['provider'])) {
            $this->coreConfig['verify_required'] = array();
            $this->coreConfig['external_mail_provider'] = true;
        } else {
            $this->coreConfig['external_mail_provider'] = false;
        }

        return $this;
    }

    /**
     * Check if Visitor is from REW Office
     *
     *  __          __     _____  _   _ _____ _   _  _____
     *  \ \        / /\   |  __ \| \ | |_   _| \ | |/ ____|
     *   \ \  /\  / /  \  | |__) |  \| | | | |  \| | |  __
     *    \ \/  \/ / /\ \ |  _  /| . ` | | | | . ` | | |_ |
     *     \  /\  / ____ \| | \ \| |\  |_| |_| |\  | |__| |
     *      \/  \/_/    \_\_|  \_\_| \_|_____|_| \_|\_____|
     *
     * THIS WAY THERE BE DRAGONS!
     *
     * This function also controls passwordless access to the backend.
     * If you are making changes BE SUPER DUPER SURE that you aren't letting
     * untrusted people access to the backend. Use BrowserStack to test!
     *
     * @return boolean
     */
    public function isREW()
    {
        $ips = $this->getCoreConfig();
        $ips = $ips['office_ips'];
        return in_array(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '', $ips);
    }
}
