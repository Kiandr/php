<?php

namespace REW\Backend\Email;

use REW\Backend\Interfaces\Email\EmailInterface;
use \Auth;
use \Backend_Mailer;
use \Format;
use \Settings;
use \Validate;
use REW\Core\Interfaces\ContainerInterface;

/**
 * Class Email
 */
class Email implements EmailInterface
{

    /**
     * Mailer Class
     * @var Backend_Mailer
     */
    protected $mailer;

    /**
     * Authuser sending this group of emails
     * @var Auth
     */
    protected $sender;

    /**
     * Email Delayed Time
     * @var int
     */
    protected $delayed;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Create Backend Mailer
     *
     * @param Auth  $authuser    Mailer Sender
     * @param array $data        Mailer Data
     * @param array $attachments Mailer Attachments
     * @param ContainerInterface $container DI container
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        Auth $authuser,
        array $data,
        array $attachments = [],
        ContainerInterface $container = null
    ) {
        if ($container === null) {
            $container = \Container::getInstance();
        }
        $this->container = $container;

        // Set Sender
        $this->sender = $authuser;

        // Check Required Fields
        $required   = array();
        $required[] = array('value' => 'email_subject', 'title' => 'Email Subject');
        $required[] = array('value' => 'email_message', 'title' => 'Email Message');

        // Process Required Fields
        foreach ($required as $require) {
            if (empty($data[$require['value']])) {
                throw new \InvalidArgumentException($require['title'] . ' is a required field.');
            }
        }

        // Require Valid CC Email
        if (!empty($data['cc_email']) && !Validate::email($data['cc_email'])) {
            $errors[] = 'Invalid CC Email Address Supplied.';
        }

        // Require Valid BCC Email
        if (!empty($data['bcc_email']) && !Validate::email($data['bcc_email'])) {
            $errors[] = 'Invalid BCC Email Address Supplied.';
        }

        // Setup Mailer
        $this->mailer = new Backend_Mailer(array(
            'html'        => ($data['is_html'] !== 'false'),        // HTML vs Plaintext
            'subject'     => $data['email_subject'],                // Email Subject
            'message'     => $data['email_message'],                // Email Message
            'template'    => $data['tmp_id'],                       // Load Template
            'cc_email'    => $data['cc_email'],                     // CC Recipient
            'bcc_email'   => $data['bcc_email'],                    // BCC Recipient
            'signature'   => $authuser->info('signature'),          // Signature
            'append'      => $authuser->info('add_sig') == 'Y'  // Append Signature
        ));

        // Default Mailer Delay
        if ($data['delay'] == 'Y') {
            $this->delayed = strtotime(date('Y-m-d', strtotime($data['send_date'])) . ' ' . date('H:i:s', strtotime($data['send_time'])));
            $this->mailer->setDelayed($this->delayed, $authuser);
        }

        // Set Mailer
        $this->mailer->setSender($authuser->info('email'), $authuser->getName());

        // Set Attachments
        $uploadErrors = [];
        $this->mailer->uploadAttachments($attachments, $uploadErrors);
        if (!empty($uploadErrors)) {
            throw new \InvalidArgumentException('An invalid file was uploaded as an attachment.');
        }
    }

    /**
     * Send an email
     *
     * @param array  $recipients Recipient information
     * @param string $recipientsType
     * @param array &$errors
     *
     * @return string Success Description
     *
     * @throws \InvalidArgumentException
     */
    public function send(array $recipients, $recipientsType = self::TYPE_LEADS, &$errors = array())
    {

        // Clone Default Mailer
        $mailer =  clone $this->mailer;

        // Get Default Delay
        $delayed = $this->delayed;

        // Check for emailing multiple recipients
        $multiple = false;
        if (count($recipients) > 1) {
            $multiple = true;

            //Delay Emails to Multiple Recipients
            if (empty($delayed)) {
                $delayed = time();
                $mailer->setDelayed($delayed, $this->sender);
            }
        }

        // Set up counter to track number of emails
        $email_count = 0;

        // Loop through recipient(s)
        foreach ($recipients as $recipient) {
            // Set Recipient
            $mailer->setRecipient($recipient['email'], $recipient['first_name'] . ' ' . $recipient['last_name']);

            // Mailer Tags
            $tags = array(
                'first_name'=> $recipient['first_name'],
                'last_name' => $recipient['last_name'],
                'email'     => $recipient['email']
            );
            if ($recipientsType == self::TYPE_LEADS) {
                //Set Unsubscribe on multiple
                if ($multiple) {
                    $mailer->setUnsubscribe(true);
                }

                // Set Guid & Verify
                if (!empty($recipient['guid'])) {
                    $tags = array_merge($tags, array(
                        'guid' => Format::toGuid($recipient['guid']),
                        'verify' => Settings::getInstance()->SETTINGS['URL_IDX'] . 'verify.html?verify=' . Format::toGuid($recipient['guid']),
                    ));
                }
            }

            // Send Mail
            if ($mailer->Send($tags)) {
                $email_count++;

                $event_user_type = $this->getEventUser($recipientsType);
                $event_user = array(
                    $eventInstance = $this->container->get($event_user_type),
                    $this->sender->getHistoryUser()
                );
                $eventInstance->setUser($recipient['id']);

                $event_data = array(
                    'plaintext' => !$mailer->isHTML(),
                    'subject'   => $mailer->getSubject(),
                    'message'   => $mailer->getMessage(),
                    'tags'      => $mailer->getTags(),
                    'sender'    => $this->sender->info('id')
                );

                // Email to be sent later
                if (!empty($delayed)) {
                    $event_data = array_merge($event_data, array(
                        'delayed'   => true,
                        'timestamp' => $delayed
                    ));

                    // Log Event: Delayed Email to Send
                    $delayedEventType = $this->getDelayedEvent($recipientsType);
                    $event = $this->container->get($delayedEventType);

                // Email sent
                } else {
                    // Log Event: Email Sent to Lead
                    $sendEventType = $this->getSendEvent($recipientsType);
                    $event = $this->container->get($sendEventType);
                }
                $event->setData($event_data);
                $event->setUsers($event_user);

                // Save to DB
                $event->save();

            // Error
            } else {
                $errors[] = 'There was an error while attempting to send this email to <strong>' . $recipient['first_name'] . ' ' . $recipient['last_name'] . '</strong>.';
            }
        }

        // Sending multiple emails
        $recipient = reset($recipients);
        return $this->buildSuccessMessage($delayed, $multiple, $recipientsType, $email_count, $recipient);
    }

    /**
     * Builds a success message and returns the string
     *
     * @param bool $delayed
     * @param bool $multiple
     * @param string $recipientsType
     * @param int $emailCount
     * @param array $recipient
     * @return string
     */
    public function buildSuccessMessage($delayed, $multiple, $recipientsType, $emailCount, $recipient)
    {
        if (!empty($multiple)) {
            if (!empty($delayed)) {
                return sprintf(
                    'Your email will be sent to %s %s on <strong>%s</strong>.',
                    $emailCount,
                    $recipientsType,
                    date('F\ jS, Y \a\t g\:iA', $delayed)
                );
            } else {
                return sprintf('Your email will be sent to %s %s.', $emailCount, $recipientsType);
            }
        } else {
            // Sending to a single recipient
            if (!empty($delayed)) {
                return sprintf(
                    'Your email will be sent to %s %s on <strong>%s</strong>.',
                    $recipient['first_name'],
                    $recipient['last_name'],
                    date('F\ jS, Y \a\t g\:iA', $delayed)
                );
            } else {
                return sprintf(
                    'Email has successfully been sent to <strong>%s %s</strong>.',
                    $recipient['first_name'],
                    $recipient['last_name']
                );
            }
        }
    }

    /**
     * Check if this mailer is configured for delayed emails
     */
    public function isDelayed()
    {
        return (bool) $this->mailer->isDelayed();
    }

    /**
     * Get Send Event Class Name
     * @param string $type Recipient Type
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getSendEvent($type)
    {
        switch ($type) {
            case self::TYPE_LEADS:
                return 'History_Event_Email_Sent';
            case self::TYPE_AGENTS:
                return 'History_Event_Email_Agent';
            case self::TYPE_ASSOCIATES:
                return 'History_Event_Email_Associate';
            case self::TYPE_LENDERS:
                return 'History_Event_Email_Lender';
            default:
                throw new \InvalidArgumentException('An invalid agent type was provided.');
        }
    }

    /**
     * Get Delayed Event Class Name
     * @param string $type Recipient Type
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getDelayedEvent($type)
    {
        switch ($type) {
            case self::TYPE_LEADS:
                return 'History_Event_Email_Delayed';
            case self::TYPE_AGENTS:
                return 'History_Event_Email_Agent';
            case self::TYPE_ASSOCIATES:
                return 'History_Event_Email_Associate';
            case self::TYPE_LENDERS:
                return 'History_Event_Email_Lender';
            default:
                throw new \InvalidArgumentException('An invalid agent type was provided.');
        }
    }

    /**
     * Get Event User Class Name
     * @param string $type Recipient Type
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getEventUser($type)
    {
        switch ($type) {
            case self::TYPE_LEADS:
                return 'History_User_Lead';
            case self::TYPE_AGENTS:
                return 'History_User_Agent';
            case self::TYPE_ASSOCIATES:
                return 'History_User_Associate';
            case self::TYPE_LENDERS:
                return 'History_User_Lender';
            default:
                throw new \InvalidArgumentException('An invalid agent type was provided.');
        }
    }
}
