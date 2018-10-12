<?php

use \PHPMailer\RewMailer as PHPMailer;

/**
 * Backend_Mailer is a wrapped to PHPMailer used for sending HTML Emails.
 *
 * Here is an example on how to use this class:
 * <code>
 * <?php
 *
 * // Mailer Configuration
 * $data = array(
 *     'subject' => 'Email Subject',
 *     'message' => 'Hello {first_name}!'
 * );
 *
 * // Message Tags
 * $tags = array(
 *     'first_name' => 'Lead'
 * );
 *
 * // Create Mailer
 * $mailer = new Backend_Mailer($data));
 *
 * // Email Recipient
 * $mailer->setRecipient('email@example.com', 'Lead Name');
 *
 * // Send Email
 * $mailer->Send($tags);
 *
 * ?>
 * </code>
 * @package Backend
 */
class Backend_Mailer
{

    /**
     * Key-value array of Mailer Data
     * @var array
     */
    protected $data = array();

    /**
     * Key-value array of Email Recipient array(email => string, name => string)
     * @var array
     */
    protected $recipient;

    /**
     * Key-value array of Email Sender array(email => string, name => string)
     * @var array
     */
    protected $sender;

    /**
     * Reply-To
     * @var array $reply array('email' => '', 'name' => '')
     */
    protected $reply;

    /**
     * PHPMailer class used for actual sending of email
     * @var PHPMailer
     */
    protected $mailer;

    /**
     * Email Subject
     * @var string
     */
    protected $subject;

    /**
     * Email Message
     * @var string
     */
    protected $message;

    /**
     * Email Tags
     * @var array
     */
    protected $tags = array();

    /**
     * Send as HTML
     * @var boolean
     */
    protected $html = true;

    /**
     * Require Unsubscribe Link (Append to Message if {unsubscribe} tag is not used)
     * @var boolean
     */
    protected $unsubscribe = false;

    /**
     * Append Signature to Message
     * @var boolean
     */
    protected $append = false;

    /**
     * Email Signature that is attached to the message if $this->append is true, and used to replace {signature} tag
     * @var string $signature
     */
    protected $signature;

    /**
     * This is the HTML Email Template is used to wrap Email Message, It requires that #body# is present to where the $this->message is placed
     * @var string $template
     */
    protected $template;

    /**
     * Store email attachments: [[file => '/tmp/phpWnxYxY', name => 'Uploaded_File.JPG', type => 'image/jpeg'], .., ..]
     * @var array $attachments
     */
    protected $attachments;

    /**
     * Allowed File Types for Email Attachments (File extensions)
     * @var array
     */
    public static $allowAttachments = array('gif', 'png', 'bmp', 'jpg', 'txt', 'pdf', 'doc', 'docx', 'rtf');

    /**
     * Delayed Email (Timestamp to Send Later)
     * @var int
     */
    protected $delayed = false;

    /**
     * Authorized Sender (Use for saving delayed emails)
     * @var Auth
     */
    protected $authuser;

    /**
     * @var PDOStatement
     */
    private $delayedStmt;

    /**
     * Construct Mailer
     *
     * @param array $data Key-value of configuration settings [html => boolean, subject => string, message => string, signature => string, template => string, append => boolean, cc_email => string, bcc_email => string]
     * @uses PHPMailer::__construct()
     */
    public function __construct($data = array())
    {

        // Mailer Data
        $this->data = $data;

        // Setup PHPMailer
        $this->mailer = new PHPMailer();
        $this->mailer->CharSet = 'UTF-8';

        // Email Subject
        if (!empty($data['subject'])) {
            $this->setSubject($data['subject']);
        }

        // Email Message
        if (!empty($data['message'])) {
            $this->setMessage($data['message']);
        }

        // HTML or Plaintext (HTML by Default)
        $this->setHTML(isset($data['html']) ? $data['html'] : true);

        // Require Unsubscribe Link
        if (!empty($data['unsubscribe'])) {
            $this->setUnsubscribe(true);
        }

        // Append Signature
        if (!empty($data['append'])) {
            $this->appendSignature(true);
        }

        // Email Signature
        if (!empty($data['signature'])) {
            $this->setSignature($data['signature']);
        }

        // Email Template
        if (!empty($data['template'])) {
            $this->setTemplate($data['template']);
        }

        // CC Email
        if (Validate::email($data['cc_email'], true)) {
            $this->getMailer()->AddCC($data['cc_email']);
        }

        // BCC Email
        if (Validate::email($data['bcc_email'], true)) {
            $this->getMailer()->AddBCC($data['bcc_email']);
        }
    }

    /**
     * Send Email
     *
     * @param array $tags Optional Tags for Replacement, {key} replaced with $tags['key]
     * @return bool
     * @throws Exception If no recipient is set
     * @uses PHPMailer::AddAddress()
     * @uses PHPMailer::send()
     * @uses DB
     */
    public function Send($tags = array())
    {

        // Require Recipient
        if (empty($this->recipient)) {
            throw new Exception('No Recipient Provided');
        }

        // Clear Previous Data
        $this->mailer->ClearReplyTos();
        $this->mailer->ClearAddresses();

        // Set Recipient
        $this->mailer->AddAddress($this->recipient['email'], $this->recipient['name']);

        // Set PHPMailer Subject
        $this->mailer->Subject = html_entity_decode($this->getSubject(), ENT_QUOTES, 'UTF-8');

        // Set PHPMailer Body
        $message = $this->getMessage($tags);
        $this->mailer->Body = $this->replaceTags($message, $tags);

        // Message is HTML
        if (!empty($this->html)) {
            $this->mailer->AltBody = 'Please View In HTML Enabled Email Client';
        }

        // Email Sender
        $sender = $this->getSender();
        if (!empty($sender['email'])) {
            $this->mailer->Sender = $sender['email'];
            $this->mailer->From = $sender['email'];
            if (!empty($sender['name'])) {
                $this->mailer->FromName = $sender['name'];
            }

        // Default Sender
        } else {
            $this->mailer->Sender = Settings::getInstance()->SETTINGS['EMAIL_NOREPLY'];
            $this->mailer->From = Settings::getInstance()->SETTINGS['EMAIL_NOREPLY'];
            $this->mailer->FromName = 'Lead Manager at ' . $_SERVER['HTTP_HOST'];
        }

        // Set Reply-To
        $reply = $this->getReplyTo();
        if (!empty($reply)) {
            $this->mailer->AddReplyTo($reply['email'], $reply['name']);
        }

        // Delayed Email, Save for Later
        if (!empty($this->delayed)) {
            try {
                // Prepare insert statement
                if (!isset($this->delayedStmt)) {
                    $this->delayedStmt = DB::get('users')->prepare(
                        "INSERT INTO `delayed_emails` SET `timestamp` = :timestamp,"
                            . " `agent` = :agent,"
                            . " `lender` = :lender,"
                            . " `associate` = :associate,"
                            . " `mailer` = :mailer,"
                            . " `message` = :message,"
                            . " `tags` = :tags"
                    );
                }

                // Insert into Delayed Emails
                $this->delayedStmt->execute([
                    'timestamp' => date('Y-m-d H:i:s', $this->delayed),
                    'agent' => $this->authuser->isAgent() ? $this->authuser->info('id') : null,
                    'lender' => $this->authuser->isLender() ? $this->authuser->info('id') : null,
                    'associate' => $this->authuser->isAssociate() ? $this->authuser->info('id') : null,
                    'mailer' => serialize($this->mailer),
                    'message' => $message,
                    'tags' => json_encode($this->getTags())
                ]);

                // Success
                return true;

            // Database Error
            } catch (PDOException $e) {
                Log::error($e);
                return false;
            }
        } else {
            // Send PHPMailer
            return $this->mailer->Send();
        }
    }

    /**
     * Replace Tags in Email Message {key} replaced with $tags[key]
     *  - {signature}
     *  - {unsubcribe}
     * @param string $message Email message
     * @param array $tags Key-value array of tags for replacement
     */
    public function replaceTags($message, &$tags = array())
    {

        // Set Signature Tag (If not already)
        if (!isset($tags['signature'])) {
            $tags['signature'] = $this->getSignature();
        }

        // Convert Signature to Plaintext
        $tags['signature'] = !empty($this->html) ? $tags['signature'] : $this->_convertToPlaintext($tags['signature']);

        $sanitize = ['name', 'first_name', 'last_name', 'email', 'search_title'];

        // Find & Replace Tags
        foreach ($tags as $tag => $value) {
            // Sanitize User Inputted Tags If Being Sent In An HTML Email
            if ($this->html && in_array($tag, $sanitize)) {
                $value = empty($value) ?: Format::htmlspecialchars($value);
            }
            $message = str_replace('{' . $tag . '}', $value, $message);
        }

        // Find & Replace {unsubscribe} Tags
        $tags['unsubscribe'] = $url = $this->getUnsubscribe(isset($tags['guid']) ? $tags['guid'] : false);
        if (!empty($this->html)) {
            $message = preg_replace_callback('#(<\s*a.+href=")?({unsubscribe})(".*>(.+)</a>)?#i', function ($matches) use ($url) {
                $matches[3] = str_replace('{unsubscribe}', $url, $matches[3]);
                // Return Anchor
                return empty($matches[1]) ? '<a href="' . $url . '" target="_blank">Unsubscribe</a>' : $matches[1] . $url . $matches[3];
            }, $message);

        // Plaintext
        } else {
            $message = str_replace('{unsubscribe}', $url, $message);
        }

        // Store Tags
        if (!empty($tags)) {
            $this->tags = $tags;
        }

        // Return Message
        return $message;
    }

    /****************** DELAYED EMAIL ******************/

    /**
     * Send as Delayed Email
     * @param int $timestamp Timestamp to Send Email
     * @param Auth $authuser Authorized Sender
     */
    public function setDelayed($timestamp, Auth $authuser = null)
    {
        $this->delayed = $timestamp;
        if (!empty($authuser)) {
            $this->authuser = $authuser;
        }
    }

    /**
     * Checks if this mailer is configured for delayed emails
     * @return bool
     */
    public function isDelayed()
    {
        return (bool) $this->delayed;
    }

    /******************* ATTACHMENTS *******************/

    /**
     * Handle Uploading of Email Attachments
     *  <code>
     *  $mailer->uploadAttachments($_FILES['attachments'], $errors);
     *  </code>
     * @param array $files $_FILES['attachments']
     * @param array $errors Append any error messages to array
     */
    public function uploadAttachments($files, &$errors = array())
    {

        // Increase Memory Limit
        ini_set('memory_limit', (64 * 1024 * 1024));

        // Upload Email Attachments
        if (isset($_FILES) && count($_FILES) > 0) {
            // Loop through uploaded files
            for ($i = 0; $i < count($files); $i++) {
                if (is_uploaded_file($files['tmp_name'][$i])) {
                    // Upload Details
                    $file = $files['tmp_name'][$i];
                    $name = $files['name'][$i];
                    $type = $files['type'][$i];

                    // File Extension
                    $extention = strtolower(end(explode('.', $name)));

                    // Check File Type
                    if (in_array($extention, self::$allowAttachments)) {
                        // Add Attachment
                        $this->attachments[] = array(
                            'file' => $file,
                            'name' => $name,
                            'type' => $type
                        );

                    // Invalid File Type
                    } else {
                        $errors[] = 'Invalid Attachment Type: ' . $name;
                    }
                }
            }

            // Add Attachments to PHPMailer Instance
            if (!empty($this->attachments)) {
                foreach ($this->attachments as $attachment) {
                    if (file_exists($attachment['file'])) {
                        // Read Attachment's Contents
                        $fp = fopen($attachment['file'], 'r');
                        $data = fread($fp, filesize($attachment['file']));
                        fclose($fp);
                        // Add Attachment to PHPMailer
                        $this->getMailer()->AddStringAttachment($data, $attachment['name'], 'base64', $attachment['type']);
                    }
                }
            }
        }
    }

    /**
     * Get Email Attachments
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /******************* SETTERS *******************/

    /**
     * Set Email Recipient
     *
     * @param string $email Recipient Email
     * @param string $name  Recipient Name
     */
    public function setRecipient($email, $name)
    {
        $this->recipient = array('email' => $email, 'name' => $name);
    }

    /**
     * Set Email Sender
     *
     * @param string $email Sender Email
     * @param string $name  Sender Name (Optional)
     * @return void
     */
    public function setSender($email, $name = '')
    {
        $this->sender = array('email' => $email, 'name' => $name);
    }

    /**
     * Set Reply-To
     *
     * @param string $email Reply-To Email
     * @param string $name  Reply-To Name (Optional)
     * @return void
     */
    public function setReplyTo($email, $name = '')
    {
        $this->reply = array('email' => $email, 'name' => $name);
    }

    /**
     * Set Email Subject
     *
     * @param string $subject Email Subject
     * @return void
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Set Email Message
     *
     * @param string $message HTML Email Message
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Require Unsubscribe Link
     *
     * @param string $signature HTML Email Signature
     * @return boolean
     */
    public function setUnsubscribe($unsubscribe)
    {
        $this->unsubscribe = $unsubscribe;
    }

    /**
     * Append Email Signature to Message
     *
     * @param boolean $append
     * @return void
     */
    public function appendSignature($append)
    {
        $this->append = $append;
    }

    /**
     * Set Email Signature
     *
     * @param string $signature HTML Email Signature
     * @return void
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    /**
     * Set Email Template HTML or Load from Template ID
     *
     * @param string|int $template Load from ID, or Set HTML Template
     * @return void
     * @uses DB::get()
     */
    public function setTemplate($template)
    {
        if (is_numeric($template)) {
            $db = DB::get('users');
            $template = $db->fetch("SELECT `template` FROM `docs_templates` WHERE `id` = '" . $template . "';");
            if (!empty($template)) {
                $this->template = $template['template'];
            }
        } elseif (is_string($template)) {
            $this->template = $template;
        }
        // Ensure HTML
        $this->setHTML(true);
    }

    /**
     * Set PHPMailer Instance
     *
     * @param PHPMailer $mailer PHP Mailer Object
     * @return void
     */
    public function setMailer(PHPMailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /******************* GETTERS *******************/

    /**
     * Get Email Recipient
     *
     * @return array
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Get Email Sender (Key-value array)
     *
     * @return array Email Sender [email => string, name => string]
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Get Email Reply-To
     *
     * @return array
     */
    public function getReplyTo()
    {
        return $this->reply;
    }

    /**
     * Get Email Subject to Send
     *
     * @return string Email Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Get HTML Email Message to Send
     *
     * @param array $tags Optional Tags for Replacement, {key} replaced with $tags['key]
     * @return string Parsed email message to be sent
     */
    public function getMessage(&$tags = array())
    {

        // Email Message
        $message = $this->message;

        // Email Template
        $template = $this->getTemplate();

        // Append Signature
        if (!empty($this->append)) {
            $signature = $this->getSignature();
            if (!stristr($message, '{signature}') && !stristr($template, '{signature}')) {
                $tags['signature'] = !empty($this->html) ? $signature : $this->_convertToPlaintext($signature);
                $message .= !empty($this->html) ? '<br>{signature}' : "\n{signature}";
            }
        }

        // Require Unsubscribe Link
        if (!empty($this->unsubscribe)) {
            $unsubscribe = $this->getUnsubscribe(isset($tags['guid']) ? $tags['guid'] : false);
            if (!stristr($message, '{unsubscribe}') && !stristr($template, '{unsubscribe}')) {
                $tags['unsubscribe'] = $unsubscribe;
                $message .= !empty($this->html) ? '<br><a href="{unsubscribe}" target="_blank">Unsubscribe</a>' : "\n" . 'Unsubscribe: {unsubscribe}';
            }
        }

        // Replace Template
        if (!empty($template)) {
            $message = str_replace('#body#', $message, $template);
        }

        // Return Message
        return $message;
    }


    /**
     * Get Email Tags
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Get Unsubscribe URL
     * @param false|string $email
     * @return false|string
     */
    public function getUnsubscribe($guid = false)
    {
        // Use Lead's GUID
        if ($guid === false) {
            $lead = $this->data['lead'];
            if (!empty($lead)) {
                $guid = Format::toGuid($lead['guid']);
            }
        }
        // Empty GUID
        if (empty($guid)) {
            return false;
        }
        // Unsubscribe URL
        return Settings::getInstance()->SETTINGS['URL'] . 'unsubscribe.php';
    }

    /**
     * Get HTML Email Signature
     *
     * @return string HTML Email Signature
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Get HTML Email Template
     *
     * @return string HTML Email Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Get PHPMailer Instance
     *
     * @return PHPMailer PHPMailer Object
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * Set Mailer to Use HTML or Plaintext
     * @param boolean $html
     */
    public function setHTML($html)
    {
        // Convert Plaintext to HTML
        if (empty($this->html) && !empty($html) && !empty($this->message)) {
            $this->message = $this->_convertToHTML($this->message);
        }
        // Toggle HTML
        $this->html = $html;
        $this->mailer->IsHTML($html);
    }

    /**
     * Check (or Set) If Maielr is HTML or Plaintext
     * @param boolean $bool
     * @return boolean TRUE if HTML, FALSE if Plaintext
     */
    function isHTML($bool = null)
    {
        if (!is_null($bool)) {
            $this->setHtml($bool);
        }
        return $this->html;
    }

    /**
     * Convert Plaintext to HTML
     * @param string $text
     * @return string
     */
    protected function _convertToHTML($text)
    {
        return nl2br($text);
    }

    /**
     * Convert HTML to Plaintext
     * @param string $html
     * @return string
     * @uses Format::stripTags
     */
    protected function _convertToPlaintext($html)
    {
        $html = str_ireplace(array('</h1>', '</h2>', '</h3>', '</p>', '</address>'), '', $html);
        $html = preg_replace('#<br\s*/?>#i', "\n", $html);
        $html = Format::stripTags($html);
        return $html;
    }

    /**
     * Magic Fallback to PHPMailer Methods
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->mailer, $name), $arguments);
    }
}
