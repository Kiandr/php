<?php

namespace REW\Api\Internal;

use Container;
use Psr\Http\Message\ServerRequestInterface;
use REW\Api\Internal\Controller\Route;
use REW\Api\Internal\Exception\ForbiddenException;
use REW\Api\Internal\Exception\InsufficientPermissionsException;
use REW\Api\Internal\Exception\ServerErrorException;
use REW\Api\Internal\Router;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\ContainerInterface;
use Slim\Slim;

/**
 * API Slim App Binding
 */
class Config
{

    /**
     * @var Slim
     */
    protected $app;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var ServerRequest
     */
    protected $serverRequest;

    /**
     * Constructor
     *
     * @param Slim $app
     * @param Router $router
     * @param ContainerInterface $container
     */
    public function __construct(
        Slim $app,
        Router $router,
        ContainerInterface $container
    ) {
        $this->app = $app;
        $this->container = $container;
        $this->router = $router;
        $this->serverRequest = $this->container->get(ServerRequestInterface::class);
        $this->auth = $this->container->get(AuthInterface::class);
    }

    /**
     * Invoke API Configuration
     */
    public function __invoke()
    {
        $this->buildSlimHooks();
        $this->buildSlimRoutes();
    }

    /**
     * Define Hooks for Slim App
     */
    protected function buildSlimHooks()
    {
        // Before
        $this->app->hook('slim.before', function () {
            $this->app->response()->header('Content-Type', 'application/json');

            // Request Headers
            $headers = $this->app->request()->headers();

            // Server Request Vars
            $server = $this->serverRequest->getServerParams();

            try {
                // Request must be made from the same host
                if ($headers['Host'] !== $server['HTTP_HOST']) {
                    throw new ForbiddenException('Invalid request host.');
                }

                // Require valid CRM authentication
                if (!$this->auth instanceof AuthInterface || !$this->auth->isValid()) {
                    throw new InsufficientPermissionsException('Failed to authenticate user session.');
                }
            } catch (ServerErrorException $e) {
                $httpCode = $e->getHttpCode();
                $httpCode = $httpCode ?: -1;
                $this->app->halt($httpCode, json_encode([
                    'error' => [
                        'code'    => $e->getCode(),
                        'message' => $e->getMessage(),
                        'type'    => $e->getType(),
                    ],
                ]));
            }
        });
    }

    /**
     * Pull the endpoint mapping for Slim App routing
     *
     * ( Must be run after bindings )
     */
    protected function buildSlimRoutes()
    {
        // CRM Endpoints
        $this->app->group('/crm', function () {

            // Action Plans
            $this->app->group('/action_plans', function () {

                $this->app->get('/', function () {
                    $this->router->render(Route\Crm\ActionPlans\Collection::class);
                });

                // Assign leads to an action plan
                $this->app->post('/:agent_id/assign', function ($actionId) {
                    $this->router->render(Route\Crm\ActionPlans\ActionPlan\Assign::class, [
                        'actionId' => $actionId,
                    ]);
                });


            });

            // Agent Endpoints
            $this->app->group('/agents', function () {

                // Pull a list of agents
                $this->app->get('/', function () {
                    $this->router->render(Route\Crm\Agents\Collection::class);
                });

                // Pull a specific agent
                $this->app->get('/:agent_id', function ($agentId) {
                    $this->router->render(Route\Crm\Agents\Agent\Get::class, [
                        'agentId' => $agentId
                    ]);
                });

                // Assign leads to an agent
                $this->app->post('/:agent_id/assign', function ($agentId) {
                    $this->router->render(Route\Crm\Agents\Agent\Assign::class, [
                        'agentId' => $agentId,
                    ]);
                });

                // Copy campaigns to an agent
                $this->app->post('/:agent_id/copy', function ($agentId) {
                    $this->router->render(Route\Crm\Agents\Agent\CopyCampaign::class, [
                        'agentId' => $agentId,
                    ]);
                });

            });

            // Lead Endpoints
            $this->app->group('/leads', function () {

                // Export Results to CSV
                $this->app->get('/export', function () {
                    $this->router->render(Route\Crm\Leads\Export\ExportCSV::class);
                });

                // Pull a list of leads
                $this->app->get('/', function () {
                    $this->router->render(Route\Crm\Leads\Collection::class);
                });

                // Assign a lead to an agent
                $this->app->post('/:lead_id/agent/:agent_id/assign', function ($leadId, $agentId) {
                    $this->router->render(Route\Crm\Leads\Lead\Agent\Assign::class, [
                        'agentId' => $agentId,
                        'leadId'  => $leadId,
                    ]);
                });

                // Unassign a lead from a agent
                $this->app->post('/:lead_id/agent/unassign', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Agent\Unassign::class, [
                        'leadId'  => $leadId,
                    ]);
                });

                // Delete a lead
                $this->app->delete('/:lead_id', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Delete::class, [
                        'leadId' => $leadId,
                    ]);
                });

                // Assign a lead to a group(s)
                $this->app->post('/:lead_id/groups/assign', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Groups\Assign::class, [
                        'leadId' => $leadId,
                    ]);
                });

                // Unassign a lead from a group(s)
                $this->app->post('/:lead_id/groups/unassign', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Groups\Unassign::class, [
                        'leadId' => $leadId,
                    ]);
                });

                // Assign a lead to a lender
                $this->app->post('/:lead_id/lender/:lender_id/assign', function ($leadId, $lenderId) {
                    $this->router->render(Route\Crm\Leads\Lead\Lender\Assign::class, [
                        'leadId'   => $leadId,
                        'lenderId' => $lenderId,
                    ]);
                });

                // Unassign a lead from a lender
                $this->app->post('/:lead_id/lender/unassign', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Lender\Unassign::class, [
                        'leadId' => $leadId,
                    ]);
                });

                // Update a lead
                $this->app->put('/:lead_id', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Update::class, [
                        'leadId' => $leadId,
                    ]);
                });

                // Send an email to a lead
                $this->app->post('/:lead_id/email/send', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Email\Send::class, [
                        'leadId' => $leadId,
                    ]);
                });

                // Verify a lead's email address
                $this->app->get('/:lead_id/email/verify', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Email\Verify::class, [
                        'leadId' => $leadId,
                    ]);
                });

                // Get Leads Inquiries OR Showing Requests
                $this->app->get('/:lead_id/inquiries/:type', function ($leadId, $type) {
                    $this->router->render(Route\Crm\Leads\Lead\Inquiries\Collection::class, [
                        'leadId' => $leadId,
                        'type'   => $type
                    ]);
                });

                // Recommend an IDX listing for a lead
                $this->app->post('/:lead_id/listing/recommend', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Listing\Recommend::class, [
                        'leadId' => $leadId,
                    ]);
                });

                // Get latest listing activity data for a lead
                $this->app->get('/:lead_id/listings', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Listings::class, [
                        'leadId' => $leadId,
                    ]);
                });

                // Get Leads Favorite, viewed or recommended Listings
                $this->app->get('/:lead_id/listings/:type', function ($leadId, $type) {
                    $this->router->render(Route\Crm\Leads\Lead\Listings\Collection::class, [
                        'leadId' => $leadId,
                        'type' => $type,
                    ]);
                });

                // Track a phone call for a lead
                $this->app->post('/:lead_id/phone/track', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Phone\Track::class, [
                        'leadId' => $leadId,
                    ]);
                });

                // Verify a lead's phone number for texting and send a text
                $this->app->post('/:lead_id/text/send', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Text\Send::class, [
                        'leadId' => $leadId,
                    ]);
                });

                // Verify a lead's phone number for texting - also allows override of lead's number
                $this->app->get('/:lead_id/text/verify', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Text\Verify::class, [
                        'leadId' => $leadId,
                    ]);
                });

                // Add a note for a lead
                $this->app->post('/:lead_id/note/add', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Note\Add::class, [
                        'leadId' => $leadId,
                    ]);
                });

                // Accept a lead
                $this->app->post('/:lead_id/accept', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Accept::class, [
                        'leadId' => $leadId,
                    ]);
                });

                // Reject a lead
                $this->app->post('/:lead_id/reject', function ($leadId) {
                    $this->router->render(Route\Crm\Leads\Lead\Reject::class, [
                        'leadId' => $leadId,
                    ]);
                });

            });

            // Email endpoints
            $this->app->get('/email/validate', function () {
                $this->router->render(Route\Crm\Email\Validate::class);
            });

            // Group Endpoints
            $this->app->group('/groups', function () {

                // Pull a list of groups
                $this->app->get('/', function () {
                    $this->router->render(Route\Crm\Groups\Collection::class);
                });

                // Assign a lead to a group(s)
                $this->app->post('/assign', function () {
                    $this->router->render(Route\Crm\Groups\Assign::class);
                });

            });

            // Lender Endpoints
            $this->app->group('/lenders', function () {

                // Pull a list of lenders
                $this->app->get('/', function () {
                    $this->router->render(Route\Crm\Lenders\Collection::class);
                });

                // Pull a specific lender
                $this->app->get('/:lender_id', function ($lenderId) {
                    $this->router->render(Route\Crm\Lenders\Lender\Get::class, [
                        'lenderId' => $lenderId,
                    ]);
                });

            });

            // "Current" (Auth)User Endpoint
            $this->app->group('/user', function () {

                // Pull the current Authuser's info
                $this->app->get('/', function () {
                    $this->router->render(Route\Crm\User\Get::class);
                });

                // "Current" (Auth)User - Inbox Events
                $this->app->group('/inbox', function () {

                    // Pull the current Authuser's upcoming Inbox Events
                    $this->app->get('/', function () {
                        $this->router->render(Route\Crm\User\Inbox\Collection::class);
                    });

                    // Dismiss a specific Inbox Event for current Authuser
                    $this->app->post('/:item_id/dismiss', function ($itemId) {
                        $this->router->render(Route\Crm\User\Inbox\Item\Dismiss::class, [
                            'itemId' => $itemId
                        ]);
                    });

                });

                // "Current" (Auth)User - Action Plan Tasks
                $this->app->group('/tasks', function () {

                    // Pull the current Authuser's upcoming Action Plan Tasks
                    $this->app->get('/', function () {
                        $this->router->render(Route\Crm\User\Tasks\Collection::class);
                    });

                    // Pull a specific Action Plan Task for current Authuser
                    $this->app->get('/:user_task_id', function ($userTaskId) {
                        $this->router->render(Route\Crm\User\Tasks\Task\Get::class, [
                            'userTaskId' => $userTaskId,
                        ]);
                    });

                    // Update a specific Action Plan Task for current Authuser
                    $this->app->put('/:user_task_id', function ($userTaskId) {
                        $this->router->render(Route\Crm\User\Tasks\Task\Update::class, [
                            'userTaskId' => $userTaskId,
                        ]);
                    });

                });

            });

            // Feeds Endpoints
            $this->app->group('/feeds', function () {

                // Pull the feeds
                $this->app->get('/', function () {
                    $this->router->render(Route\Crm\Feeds\Collection::class);

                });
            });
            
            // Agent Endpoints
            $this->app->group('/settings', function () {

                // Pull a list of agents
                $this->app->get('/', function () {
                    $this->router->render(Route\Crm\Settings\Collection::class);
                });

            });

        });
    }

}