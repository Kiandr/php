<?php

namespace REW\Api\Internal\Controller\Route\Crm\User\Inbox\Item;

use REW\Api\Internal\Exception\BadRequestException;
use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Exception\ServerSuccessException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Response;
use Slim\Http\Request;

/**
 * User Inbox Item Dismiss Controller
 * @package REW\Api\Internal\Controller
 */
class Dismiss implements ControllerInterface
{
    /**
     * @var array
     */
    const MODES = ['register', 'message', 'inquiry', 'showing', 'selling'];

    /**
     * @var AuthInterface
     */
    protected $auth;

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var int
     */
    protected $itemId;

    /**
     * @var array
     */
    protected $post;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param SettingsInterface $settings
     */
    public function __construct(
        AuthInterface $auth,
        DBInterface $db,
        SettingsInterface $settings
    ){
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

        $this->itemId = intval($this->routeParams['itemId']);

        $this->checkValidRequest();

        $this->checkDismissed();

        $this->dismissItem();
    }

    /**
     * Check if the requested item has already been dismissed
     * @throws BadRequestException
     */
    protected function checkDismissed()
    {
        $sql = sprintf(
            "SELECT * FROM `%s` "
            . " WHERE `agent` = :agent_id "
            . " AND `event_id` = :item_id "
            . " AND `event_mode` = :item_mode "
            . ";",
            $this->settings->TABLES['LM_EVENT_DISMISSED']
        );
        $params = [
            'agent_id' => $this->auth->info('id'),
            'item_id' => $this->itemId,
            'item_mode' => $this->post['mode']
        ];
        $check = $this->db->fetch($sql, $params);
        if (!empty($check)) {
            throw new BadRequestException('The requested item has already been dismissed');
        }
    }

    /**
     * Check permissions and mode
     * @throws BadRequestException
     * @throws InsufficientPermissionsException
     * @throws NotFoundException
     */
    protected function checkValidRequest()
    {
        // Limit inbox to agents
        if (!$this->auth->isAgent()) {
            throw new InsufficientPermissionsException('Invalid user type.');
        }

        // Make sure requested mode is valid
        if (empty($this->post['mode']) || !in_array($this->post['mode'], self::MODES)) {
            throw new BadRequestException('Invalid mode.');
        }

        // Make sure a numeric itemId was provided
        if ($this->itemId <= 0) {
            throw new NotFoundException('Invalid item ID.');
        }
    }

    /**
     * Dismiss the requested item
     * @throws ServerSuccessException
     */
    protected function dismissItem()
    {
        $sql = sprintf(
            "INSERT INTO `%s` SET "
            . " `agent` = :agent_id, "
            . " `event_id` = :item_id, "
            . " `event_mode` = :item_mode "
            . ";",
            $this->settings->TABLES['LM_EVENT_DISMISSED']
        );
        $params = [
            'agent_id' => $this->auth->info('id'),
            'item_id' => $this->itemId,
            'item_mode' => $this->post['mode']
        ];

        $query = $this->db->prepare($sql);
        if ($query->execute($params)) {
            throw new ServerSuccessException('The item has been dismissed successfully.');
        }
    }
}