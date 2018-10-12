<?php

namespace REW\Backend\Controller\Leads\Lead\Forms;

use REW\Backend\Controller\Leads\Lead\AbstractLeadController;
use REW\Backend\Exceptions\PageNotFoundException;
use REW\Backend\Exceptions\SystemErrorException;
use REW\Backend\Exceptions\MissingIdException;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\Email\Email;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Backend\Auth\Leads\LeadAuth;
use \Util_IDX;
use \Exception;
use \InvalidArgumentException;

/**
 * ViewController
 * @package REW\Backend\Controller\Leads\Lead
 */
class ViewController extends AbstractLeadController
{

    /**
     * @var NoticesCollectionInterface
     */
    protected $notices;

    /**
     * @var FactoryInterface
     */
    protected $view;

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * Lead array
     * @var Backend_Lead
     */
    protected $lead;

    /**
     * Lead Authentication
     * @var AuthInterface
     */
    protected $leadAuth;

    /**
     * @var LogInterface
     */
    protected $log;

    /**
     * ViewController constructor.
     * @param NoticesCollectionInterface $notices
     * @param FactoryInterface $view
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param SettingsInterface $settings
     * @param LogInterface $log
     */
    public function __construct(
        NoticesCollectionInterface $notices,
        FactoryInterface $view,
        AuthInterface $auth,
        DBInterface $db,
        SettingsInterface $settings,
        LogInterface $log
    ) {
        $this->notices = $notices;
        $this->view = $view;
        $this->auth = $auth;
        $this->db = $db;
        $this->settings = $settings;
        $this->log = $log;
    }

    /**
     * @throws SystemErrorException If domain not found to manage
     * @throws PageNotFoundException If invalid filter selected
     */
    public function __invoke()
    {

        // Success
        $success = [];

        // Error
        $errors = [];

        // Get Valid Lead
        $this->lead = $this->getLeadFromRequest();

        // Check Lead Authorization
        $this->leadAuth = new LeadAuth($this->settings, $this->auth, $this->lead);
        if (!$this->leadAuth->canViewForms()) {
            throw new UnauthorizedPageException('You do not have permission to view lead Forms');
        }

        // Load Form
        $formId = $this->getFormFromRequest();
        if (!empty($formId)) {
            $formData = $this->loadLeadFormFromId($formId);
        }
        if (empty($formData)) {
            throw new MissingIdException('A form with the requested ID does not exist');
        }

        if ($this->checkSubmitting()) {
            if (!$this->leadAuth->canEmailLead()) {
                throw new UnauthorizedPageException('You do not have permission to email this lead.');
            }

            try {
                $emailData = $this->getEmailFromRequest();
                $this->sendEmail($emailData, $this->lead);
                $this->updateFormWithReply($formData);

                // Set success message and save notifications
                $this->notices->success(sprintf('Your reply has been sent to %s.', $this->lead->getNameOrEmail()));

                // Redirect to fields list
                header(sprintf('Location: %sleads/lead/forms/?id=%s&form=%s', URL_BACKEND, $this->lead->getId(), $formId));
                exit;
            } catch (InvalidArgumentException $e) {
                $this->notices->error(sprintf('Your reply could not be sent to %s.', $this->lead->getNameOrEmail()));
                $this->log->error($e);
            }
        }

        // Set notifications
        $this->auth->setNotices($success, $errors);

        // Render template file
        echo $this->view->render('::partials/lead/summary', [
            'title' => 'Lead Forms',
            'lead' => $this->lead,
            'leadAuth' => $this->leadAuth
        ]);

        // Render template file
        echo $this->view->render('::pages/leads/lead/forms/view', [
            'auth' => $this->auth,
            'lead' => $this->lead,
            'form' => $this->parseForm($formData),
            'canEmail' => $this->leadAuth->canEmailLead()
        ]);
    }

    /**
     * Send email to lead
     * @param array $data
     * @param Backend_Lead $lead
     */
    protected function sendEmail($data, $lead)
    {

        // Create Recipient from lead
        $recipient = [
            'id' => $lead->getId(),
            'first_name' => $lead->info('first_name'),
            'last_name' => $lead->info('last_name'),
            'email' => $lead->getEmail(),
            'guid' => $lead->info('guid'),
        ];

        $emailer = new Email($this->auth, $data?: []);
        $emailer->send([$recipient], Email::TYPE_LEADS, $errors);
    }

    /**
     * Update form  read/replied flags
     * @param array $form
     */
    protected function updateFormWithReply(array $form)
    {

        // Get Lead Form
        $leadFormUpdateQuery = $this->db->prepare(
            'UPDATE `users_forms` SET `reply` = 1'
            . (isset($form['timestamp']) ? ', `read` = NOW()' : '')
            . ' WHERE `id` = :id AND `user_id` = :user_id'
        );
        $leadFormUpdateQuery->execute(['id' => $form['id'], 'user_id' => $this->lead->getId()]);
    }

    /**
     * Parse Form
     * @param array $formData
     * @return array
     */
    protected function parseForm(array $formData)
    {
        $data = unserialize($formData['data']);

        // Get Name
        $name = ($formData['form'] === 'IDX Inquiry' && !empty($data['inquire_type']))
            ? $data['inquire_type']
            : $formData['form'];

        // Get Address
        $address = (!empty($data['address']))
            ? $data['address']
            : implode(', ', array_filter([$data['fm-addr'], $data['fm-town'], $data['fm-state'], $data['fm-postcode']]));

        // Get Form Listing
        if (!empty($data['ListingMLS']) || !empty($data['mls_number'])) {
            $mls = $data['ListingMLS'] ?: $data['mls_number'];
            $listing = $this->getListing($mls, $data['ListingType'] ?: null, $data['ListingFeed'] ?: null);
        }

        return [
            'id' => $formData['id'],
            'subject' => 'RE: ' . $name . (!empty($address) ? ' - ' . $address : ''),
            'name' => $name,
            'page' => $formData['page'],
            'data' => $this->parseData($data),
            'reply' => $formData['reply'],
            'read' => !empty($formData['read']) ? strtotime($formData['read']) : null,
            'listing' => $listing,
            'timestamp' => strtotime($formData['timestamp'])
        ];
    }

    /**
     * Parses form data for ease of reading
     * @param array $data
     * @return array
     */
    protected function parseData(array $data)
    {
        $formattedData = [];
        if (!empty($data)) {
            $data = $this->parseHoneypot($data);
            foreach ($data as $title => $value) {
                if (empty($value)) {
                    continue;
                }

                $title = $this->sanitizeTitle($title);

                // Sanitize title and value
                if (is_array($value)) {
                    $value = array_filter($value);
                    $formattedSubData = [];
                    foreach($value as $field => $data){
                        $field = $this->sanitizeTitle($field);
                        $formattedSubData[$field] = $data;
                    }
                    $value = $formattedSubData;
                }
                $formattedData[$title] = $value;
            }
        }
        return $formattedData;
    }

    /**
     * Sanitize title data
     * @param string $title
     * @return string
     */
    protected function sanitizeTitle($title){
        // split camel case
        $title = implode(' ', preg_split('/(?<=[a-z])(?=[A-Z])/', $title));
        $title = str_replace('-', ' ', str_replace('_', ' ', $title));

        // ensure common casing
        $titlePieces = explode(' ', $title);
        if (!empty($titlePieces)) {
            foreach ($titlePieces as $k => $titlePiece) {
                if (!in_array($titlePiece, ['IDX','MLS'])) {
                    $titlePieces[$k] = ucwords(strtolower($titlePiece));
                }
            }
            $title  = implode(' ', $titlePieces);
        }
        return $title;
    }

    /**
     * Remove honeypot variables and add real names
     * @param array $data
     * @return array
     */
    protected function parseHoneypot(array $data = [])
    {

        unset($data['email']);
        unset($data['first_name']);
        unset($data['last_name']);

        if (!empty($data['mi0moecs'])) {
            $data = ['email' => $data['mi0moecs']] + $data;
        }
        if (!empty($data['sk5tyelo'])) {
            $data = ['last_name' => $data['sk5tyelo']] + $data;
        }
        if (!empty($data['onc5khko'])) {
            $data = ['first_name' => $data['onc5khko']] + $data;
        }

        unset($data['mi0moecs']);
        unset($data['onc5khko']);
        unset($data['sk5tyelo']);

        return $data;
    }

    /**
     * Get Form from Id
     * @param int $id
     * @return array
     */
    protected function loadLeadFormFromId($id)
    {
        // Get Lead Form
        $leadFormQuery = $this->db->prepare('SELECT * FROM `users_forms` WHERE `id` = :id AND `user_id` = :user_id');
        $leadFormQuery->execute(['id' => $id, 'user_id' => $this->lead->getId()]);
        return $leadFormQuery->fetch();
    }

    /**
     * Is the form being submitted
     * @return boolean
     */
    protected function checkSubmitting()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Get Form Id From Request
     * @return int|null
     */
    protected function getFormFromRequest()
    {
        return isset($_POST['form']) ? $_POST['form'] : $_GET['form'];
    }

    /**
     * Get Email from  Request
     * @return array
     */
    protected function getEmailFromRequest()
    {
        return [
            'email_subject' => $_POST['email_subject'],
            'email_message' => $_POST['email_message']
        ];
    }

    /**
     * Get Form Listing
     * @param string $mls
     * @param string|null $type
     * @param string|null $feed
     * @return array|null
     */
    protected function getListing($mls, $type = null, $feed = null)
    {

        try {
            // Switch
            if ($feed) {
                Util_IDX::switchFeed($feed);
            }

            // IDX objects
            $idx = Util_IDX::getIdx();
            $db_idx = Util_IDX::getDatabase();

            // Load Listing
            $listing = $db_idx->fetchQuery(
                "SELECT SQL_CACHE " . $idx->selectColumns()
                . " FROM `" . $idx->getTable() . "`"
                . " WHERE `" . $idx->field('ListingMLS') . "` = '" . $db_idx->cleanInput($mls) . "'"
                . (!empty($type) ? " AND `" . $idx->field('ListingType') . "` = '" . $db_idx->cleanInput($type) . "'" : "")
                . " LIMIT 1;"
            );

            // Parse Listing
            if (!empty($listing)) {
                return Util_IDX::parseListing($idx, $db_idx, $listing);
            } else {
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }
}
