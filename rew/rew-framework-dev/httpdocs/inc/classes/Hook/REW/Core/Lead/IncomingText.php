<?php

/**
 * Hook_REW_Core_Lead_IncomingText
 * This hook is invoked when an incoming text message is received
 *  1. Same phone number to database and save verified timestamp
 *  2. If message is a STOPWORD or STARTWORD - update optout settings
 *  3. Text message is forwarded to lead's assigned agent (via Email)
 * @package Hooks
 */
class Hook_REW_Core_Lead_IncomingText extends Hook
{

    /**
     * Words to trigger opt-out
     * @var array
     */
    protected static $STOPWORDS = array('STOP', 'STOPALL', 'UNSUBSCRIBE', 'CANCEL', 'END', 'QUIT');

    /**
     * Words to trigger opt-in
     * @var array
     */
    protected static $STARTWORDS = array('START', 'YES');

    /**
     * Run the hook's code
     * @param string $to
     * @param string $from
     * @param string $body
     * @param string|array $media
     * @param array $request
     */
    protected function invoke($to, $from, $body, $media, $request)
    {

        // DB connection
        $db = DB::get();

        // Trim received message
        $checkword = strtoupper(trim($body));

        // Opt-in
        $optin = null;
        $optin = in_array($checkword, self::$STOPWORDS) ? 'out' : $optin;
        $optin = in_array($checkword, self::$STARTWORDS) ? 'in' : $optin;

        // Verify phone number & save optin status
        $db->prepare("INSERT INTO `twilio_verified` SET "
            . "`phone_number`	= :phone_number, "
            . "`optout`			= CASE :optin WHEN 'in' THEN NULL WHEN 'out' THEN NOW() ELSE `optout` END, "
            . "`verified`		= IFNULL(`verified`, NOW()), "
            . "`created_ts`		= NOW()"
            . " ON DUPLICATE KEY UPDATE "
            . "`optout`			= CASE :optin WHEN 'in' THEN NULL WHEN 'out' THEN NOW() ELSE `optout` END, "
            . "`verified`		= IFNULL(`verified`, NOW()), "
            . "`updated_ts`		= NOW()"
        . ";")->execute(array(
            'phone_number'  => $from,
            'optin'         => $optin
        ));

        // Update opt_texts status
        $opt_texts = $db->prepare("UPDATE `users` SET `opt_texts` = :opt_texts WHERE `id` = :lead_id;");

        // Find leads assigned to 'From' number
        $notify = array();
        $query = $db->prepare("SELECT `tvu`.`user_id`, `u`.`agent` FROM `users` `u` LEFT JOIN `twilio_verified_user` `tvu` ON `tvu`.`user_id` = `u`.`id` WHERE `tvu`.`phone_number` LIKE :phone_number;");
        $query->execute(array('phone_number' => '%' . str_replace(['+1', '+'], '', $from)));
        foreach ($query->fetchAll() as $lead) {
            // Notify assigned agent
            $lead_id = $lead['user_id'];
            $agent_id = $lead['agent'];
            $notify[$agent_id][] = $lead_id;

            // Update opt_texts
            if (!empty($optin)) {
                $opt_texts->execute(array(
                    'opt_texts' => $optin,
                    'lead_id' => $lead_id
                ));
            }

            // Opt-in request
            if ($optin === 'in') {
                (new History_Event_Text_OptIn(array(
                    'to'        => $to,
                    'from'      => $from,
                    'body'      => $body,
                    'media'     => $media,
                    'request'   => $request
                ), array(
                    new History_User_Lead($lead_id),
                    new History_User_Agent($agent_id)
                )))->save($db);

            // Opt-out request
            } else if ($optin === 'out') {
                (new History_Event_Text_OptOut(array(
                    'to'        => $to,
                    'from'      => $from,
                    'body'      => $body,
                    'media'     => $media,
                    'request'   => $request
                ), array(
                    new History_User_Lead($lead_id),
                    new History_User_Agent($agent_id)
                )))->save($db);

            // Track incoming text message
            } else {
                (new History_Event_Text_Incoming(array(
                    'to'        => $to,
                    'from'      => $from,
                    'body'      => $body,
                    'media'     => $media,
                    'request'   => $request
                ), array(
                    new History_User_Lead($lead_id),
                    new History_User_Agent($agent_id)
                )))->save($db);
            }
        }

        // FWD incoming text message
        if (!empty($notify)) {
            foreach ($notify as $agent_id => $lead_ids) {
                (new Backend_Mailer_IncomingText(array(
                    'to'        => $to,
                    'from'      => $from,
                    'body'      => $body,
                    'media'     => $media,
                    'agent_id'  => $agent_id,
                    'lead_ids'  => $lead_ids
                )))->Send();
            }
        }
    }
}
