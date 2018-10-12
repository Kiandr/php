<?php

namespace REW\Api\Internal\Controller\Route\Crm\Agents\Agent;

use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Exception\MissingParameterException;
use REW\Api\Internal\Exception\ServerSuccessException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Backend\Auth\LeadsAuth;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;
use \Backend_Agent;

/**
 * Agent Copy Campaigns
 * @package REW\Api\Internal\Controller
 */
class CopyCampaign implements ControllerInterface
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var array
     */
    protected $agents;

    /**
     * @var array
     */
    protected $campaigns;

    /**
     * @var DB
     */
    protected $db;

    /**
     * @var array
     */
    protected $post;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var ALL
     */
    const ALL = 'all';

    /**
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param SettingsInterface $settings
     */
    public function __construct(
        AuthInterface $auth,
        DBInterface $db,
        SettingsInterface $settings
    ) {
        $this->auth = $auth;
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     * @return array
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        $body = json_decode($request->getBody());
        $this->post = (!empty($body) ? (array) $body : []);
        $this->routeParams = $routeParams;

        $this->checkPermissions();

        $this->fetchAgents();

        $this->fetchCampaigns();

        $this->copyCampaigns();

        // If we've made it this far the call was successful
        $success = [sprintf(
            '<p><strong>%s:</strong> %s</p>',
            _('Note'),
            __('Agents will need to update copied campaigns to provide sender information and campaign groups.')
        )];
        $this->auth->setNotices($success);

        throw new ServerSuccessException($success[0]);
    }

    /**
     * Check the user's permissions for this request
     *
     * @throws InsufficientPermissionsException
     */
    protected function checkPermissions()
    {
        // Check Request VS Permissions
        $leadsAuth = new LeadsAuth($this->settings);

        if (!$leadsAuth->canManageCampaigns($this->auth)) {
            throw new InsufficientPermissionsException('You do not have the proper CRM permission to manage campaigns.');
        }
    }

    /**
     * Fetch agent rows into $this->agents
     *
     * @throws NotFoundException
     */
    protected function fetchAgents()
    {
        // If copying to all agents
        if ($this->routeParams['agentId'] === static::ALL) {
            $this->agents = $this->db->fetchAll(sprintf(
                "SELECT `id`, `first_name`, `last_name` FROM `%s`;",
                $this->settings->TABLES['LM_AGENTS']
            ));
        // If copying to single agent
        } else if (!empty($this->routeParams['agentId'])) {
            $this->agents = $this->db->fetchAll(sprintf(
                "SELECT `id`, `first_name`, `last_name` FROM `%s` WHERE `id` = :id;",
                $this->settings->TABLES['LM_AGENTS']
            ), ['id' => $this->routeParams['agentId']]);
        }

        if (empty($this->agents)) {
            throw new NotFoundException('Failed to find an agent with the requested ID.');
        }
    }

    /**
     * Fetch campaign rows into $this->campaigns
     *
     * @throws NotFoundException
     */
    protected function fetchCampaigns()
    {
        if (empty($this->post['campaign_ids'])) {
            throw new MissingParameterException('Missing selected campaign(s) to copy');
        }

        $this->campaigns = $this->db->fetchAll(
            sprintf(
                "SELECT `id`, `agent_id`, `name`, `description` FROM `%s` WHERE `id` IN (%s);",
                $this->settings->TABLES['LM_CAMPAIGNS'],
                implode(', ', $this->post['campaign_ids'])
            )
        );

        if (empty($this->campaigns)) {
            throw new NotFoundException('Failed to find campaigns with the requested IDs.');
        }
    }

    /**
     * Create a new category for documents given the campaign copied
     *
     * @param $agent
     * @param $campaign
     * @return int
     */
    protected function addDocumentCategory($agent, $campaign)
    {
        // Create new category name
        $categoryName = sprintf(
            "%s %s - %s",
            $agent['first_name'],
            $agent['last_name'],
            $campaign['name']
        );

        // Insert new category
        $query = sprintf(
            "INSERT INTO `%s` SET `agent_id` = :agent, `name` = :name, `description` = :description;",
            $this->settings['TABLES']['DOCUMENTS_CATEGORIES']
        );
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'agent' => $agent['id'],
            'name' => $categoryName,
            'description' => 'Campaign Documents for' . $categoryName
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Copies original documents and emails to the new campaign
     *
     * @param array $originalCampaign
     * @param int $newCampaignId
     * @param int $catId
     */
    protected function copyCampaignDocumentsAndEmails($originalCampaign, $newCampaignId, $catId)
    {
        // Get documents and emails to copy
        $select = sprintf(
            "SELECT t1.name, t1.document, t1.is_html, t2.subject, t2.send_delay FROM `%s` t1 LEFT JOIN `%s` t2 ON t1.id = t2.doc_id WHERE t2.campaign_id = :campaignId;",
            $this->settings['TABLES']['DOCUMENTS'],
            $this->settings['TABLES']['LM_CAMPAIGNS_EMAILS']
        );
        $stmt = $this->db->prepare($select);
        $stmt->execute(['campaignId' => $originalCampaign['id']]);
        $docs = $stmt->fetchAll();

        // Create new documents and emails
        $documents = sprintf("INSERT INTO `%s` SET `cat_id` = :catId, `name`  = :name, `document` = :document, `is_html` = :isHtml;",
            $this->settings['TABLES']['DOCUMENTS']
        );
        $docStmt = $this->db->prepare($documents);

        $emails = sprintf(
            "INSERT INTO `%s` SET `campaign_id` = :campaignId, `doc_id` = :docId, `subject` = :subject, `send_delay` = :sendDelay;",
            $this->settings['TABLES']['LM_CAMPAIGNS_EMAILS']
        );
        $emailStmt = $this->db->prepare($emails);

        foreach ($docs as $doc) {
            $docStmt->execute([
                'catId' => $catId,
                'name' => $doc['name'],
                'document' => $doc['document'],
                'isHtml' => $doc['is_html']
            ]);

            // Most recent document
            $docId = $this->db->lastInsertId();

            $emailStmt->execute([
                'campaignId' => $newCampaignId,
                'docId' => $docId,
                'subject' => $doc['subject'],
                'sendDelay' => $doc['send_delay']
            ]);
        }
    }

    /**
     * Create the new instances of the campaigns
     */
    protected function copyCampaigns()
    {
        // Copy Campaign
        $query = sprintf(
            "INSERT INTO `%s` SET `agent_id` = :agent, `name` = :name, `description` = :description;",
            $this->settings['TABLES']['LM_CAMPAIGNS']
        );
        $stmt = $this->db->prepare($query);

        foreach ($this->agents as $agent) {
            foreach ($this->campaigns as $campaign) {
                // Copy campaign to agent
                $stmt->execute([
                    'agent' => $agent['id'],
                    'name' => $campaign['name'],
                    'description' => $campaign['description']
                ]);

                // Target id for Document and Email copying
                $newCampaignId = $this->db->lastInsertId();

                if ($catId = $this->addDocumentCategory($agent, $campaign)) {
                    $this->copyCampaignDocumentsAndEmails($campaign, $newCampaignId, $catId);
                }
            }
        }
    }
}
