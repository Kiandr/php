<?php

/**
 * Backend_Mailer_SMS
 * @package Mailer
 */
class Backend_Mailer_SMS extends Backend_Mailer
{

    /**
     * SMS Recipient array(email => string, name => string)
     * @var array
     */
    protected $sms_recipient;

    /**
     * SMS Message (Plaintext)
     * @var string
     */
    protected $sms_message;

    /**
     * Construct SMS Mailer
     * @see Backend_Mailer::__construct()
     */
    public function __construct($data = array())
    {

        // Set SMS Message
        if (!empty($data['sms_message'])) {
            $this->setSmsMessage($data['sms_message']);
        }

        // Construct Mailer
        parent::__construct($data);
    }

    /**
     * Set SMS Recipient
     * @param string $email Recipient SMS Email
     * @param string $name  Recipient Name
     */
    public function setSmsRecipient($email, $name)
    {
        $this->sms_recipient = array('email' => $email, 'name' => $name);
    }

    /**
     * Set SMS Message (Plaintext)
     *
     * @param string $message SMS Message
     * @return void
     */
    public function setSmsMessage($sms_message)
    {
        $this->sms_message = $sms_message;
    }

    /**
     * Get SMS Recipient
     *
     * @return array
     */
    public function getSmsRecipient()
    {
        return $this->sms_recipient;
    }

    /**
     * Get SMS Message (Plaintext)
     *
     * @param array $tags Optional Tags for Replacement, {key} replaced with $tags['key]
     * @return string Parsed SMS message to be sent
     */
    public function getSmsMessage($tags = array())
    {
        return $this->replaceTags($this->sms_message, $tags);
    }

    /**
     * Send SMS Message
     * @see Backend_Mailer::Send()
     */
    public function Send($tags = array())
    {

        // Check SMS Recipient
        $sms_recipient = $this->getSmsRecipient();
        if (!empty($sms_recipient)) {
            // Use Tiny URLs for SMS Message
            $message = strip_tags($this->getSmsMessage());
            preg_replace_callback("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", function ($matches) use (&$message) {
                $message = str_replace($matches[2], Format::tinyUrl($matches[2]), $message);
            }, $message);

            // Send SMS Message
            $mailer = new Backend_Mailer();
            $mailer->setHtml(false);
            $mailer->setMessage($message);
            $mailer->setRecipient($sms_recipient['email'], $sms_recipient['name']);

            // Use 'sendmail' for Mandrill
            if ($mailer->getMailer()->UsesMandrill()) {
                $mailer->getMailer()->IsSendmail();
            }

            // Send SMS Message
            $send = $mailer->Send($tags);
        }

        try {
            // Send HTML Email
            return parent::Send($tags);

        // Exception Caught, Return Status of SMS
        } catch (Exception $e) {
            return $send;
        }
    }
}
