<?php

namespace REW\Api\Internal\Controller\Route\Crm\Feeds;

use REW\Api\Internal\Interfaces\ControllerInterface;
use REW\Core\Interfaces\SettingsInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Collection Controller
 * @package REW\Api\Internal\Controller
 */
class Collection implements ControllerInterface
{

    /**
     * @var array
     */
    protected $get;

    /**
     * @var array
     */
    protected $feeds;

    /**
     * @param AuthInterface $auth
     * @param DBInterface $db
     */
    public function __construct(
        SettingsInterface $settings
    ){
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        $this->get = $request->get();

        $body = $this->getFeeds();
        $response->setBody(json_encode($body));
    }

    /**
     * Fetch the feeds collection
     *
     * @return array
     */
    protected function getFeeds()
    {
        return $this->settings['IDX_FEEDS'];
    }

}
