<?php

/**
 * Hook_REW_SharkTank_Lead_Created
 * This hook is invoked on lead register/connect for new leads only
 * @package Hooks
 */
class Hook_REW_SharkTank_Lead_Created extends Hook
{

    /**
     * @var \Backend_Lead
     */
    protected $lead;

    /**
     * Invoke this hook
     *
     * @param Backend_Lead $lead
     * @param bool $manually_created
     * @return void
     */
    protected function invoke(Backend_Lead $lead, $manually_created = false)
    {
        // Don't run Shark Tank routing for a manually created lead
        if (!$manually_created) {
            // Make sure we have all lead data available
            $this->lead = Backend_Lead::load($lead->getId());

            $db = DB::get();
            $settings = Settings::getInstance();

            // Add Lead to Shark Tank if Applicable
            if (!empty($settings->MODULES['REW_SHARK_TANK'])) {
                try {
                    $default_info = $db->fetch("SELECT `shark_tank` FROM `default_info` WHERE `agent` = 1;");
                    if ($default_info['shark_tank'] === 'true'
                        && $this->lead->info('agent') === '1'
                        && $this->lead->info('status') === 'unassigned'
                        && $this->lead->info('in_shark_tank') !== 'true'
                    ) {
                        $shark_tank_assign = $db->prepare("UPDATE `users` SET `in_shark_tank` = 'true', `timestamp_in_shark_tank` = NOW() WHERE `id` = :user_id;");
                        if ($shark_tank_assign->execute(['user_id' => $this->lead->getId()])) {
                            try {
                                $agents = $db->fetchAll("SELECT `id` FROM `agents`;");
                                foreach ($agents as $agent) {
                                    $this->notifyAgent($agent['id']);
                                }
                            } catch (Exception $e) {
                                Log::Error($e->getMessage());
                            }
                        }
                    }
                } catch (Exception $e) {
                    if ($settings->isREW()) {
                        echo $e->getMessage() . PHP_EOL;
                    }
                }
            }
        }
    }

    /**
     * Check Agent Notification Settings and Send Notification Appropriately
     *
     * @param int $agent_id
     */
    protected function notifyAgent($agent_id)
    {
        $agent = Backend_Agent::load($agent_id);
        // Check if Agent has Access to the Shark Tank
        if ($agent->hasPermission(\REW\Core\Interfaces\AuthInterface::PERM_SHARK_TANK)) {
            // Init SMS Mailer
            $mailer = new Backend_Mailer_SMS();
            // Check if Agent is opted in for Shark Tank notifications
            if ($agent->checkIncomingNotifications($mailer, Backend_Agent_Notifications::INCOMING_SHARK_TANK_LEADS)) {
                $mailer->setSender(Settings::getInstance()->SETTINGS['EMAIL_NOREPLY'], 'CRM');
                $mailer->setSubject(sprintf('New Shark Tank Lead Available on %s', $_SERVER['HTTP_HOST']));
                // Email Message
                $mailer->setMessage($this->buildEmailBody($agent));
                // SMS Message
                if (!empty($mailer->getSmsRecipient())) {
                    $mailer->setSmsMessage(sprintf(
                        'New Shark Tank lead: %s',
                        Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/sharktank-' . $this->lead->getId() . '/'
                    ));
                }
                // Send Notifications
                if (!$mailer->Send()) {
                    if ($settings->isREW()) {
                        echo sprintf('Failed to send shark tank update email to agent: %s', $agent->info('email'));
                    }
                }
            }
        }
    }

    /**
     * Generate the notification email's body
     *
     * @param Backend_Agent $agent
     * @return string
     */
    protected function buildEmailBody(Backend_Agent $agent)
    {
        $message = '<p>A new lead is available in the Shark Tank.</p>';
        $message .= sprintf(
            '<p><a href="%sleads/sharktank-%s/">Click here</a> to claim this lead.</p>',
            Settings::getInstance()->URLS['URL_BACKEND'],
            $this->lead->getId()
        );
        $message .= '<========================><br>';
        $message .= sprintf(
            '<strong>Name:</strong> %s %s<br>',
            htmlspecialchars($this->lead->info('first_name')),
            htmlspecialchars($this->lead->info('last_name'))
        );
        if (!empty($this->lead->info('value')) && $this->lead->info('value') > 0) {
            $message .= sprintf('<strong>Value:</strong> %s<br>', Format::number($this->lead->info('value')));
        }
        $message .= '<========================>';
        return $message;
    }
}
