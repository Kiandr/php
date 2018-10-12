<?php

namespace Modules\Elite\BlogLatest;

use REW\Core\Interfaces\ModuleInterface;
use REW\Core\Interfaces\ContainerInterface;
use REW\Core\Interfaces\ModuleControllerInterface;
use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\Util\ModuleControllerInterface as UtilModuleControllerInterface;

class BlogLatestController implements ModuleControllerInterface
{
    /**
     * @var UtilModuleControllerInterface
     */
    private $utilController;

    /**
     * @var ModuleInterface
     */
    private $module;

    /**
     * @var DBFactoryInterface
     */
    private $dbFactory;

    /**
     * BlogLatestController constructor.
     * @param ModuleInterface $module
     * @param ContainerInterface $container
     * @param DBFactoryInterface $dbFactory
     */
    public function __construct(ModuleInterface $module, ContainerInterface $container, DBFactoryInterface $dbFactory)
    {
        $this->module = $module;
        $this->dbFactory = $dbFactory;
        $this->utilController = $container->make(UtilModuleControllerInterface::class, ['module' => $module]);
    }

    /**
     * Display Resource
     * @param bool $return Return Output
     * @return mixed
     */
    public function display($return = false)
    {
        // Title (Default: False)
        $title = $this->module->config('title') ? $this->module->config('title') : false;

        // Limit (Default: False)
        $limit = $this->module->config('limit') ? (int) $this->module->config('limit') : false;

        // Load blog posts
        $posts = $this->getPosts($limit);

        return $this->utilController->render(['title' => $title, 'posts' => $posts], $return);
    }

    /**
     * Get $limit number of recent blog posts
     * @param int $limit
     * @return mixed
     */
    public function getPosts($limit)
    {
        // Get CMS Database
        $db = $this->dbFactory->get('cms');

        // Limit (Default: False)
        if ($limit) {
            $limit = "LIMIT " . ((int) $limit);
        }

        // Get Latest posts
        return $db->fetchAll('SELECT `timestamp_published`, `title`, `link`, `tags` FROM `blog_entries` WHERE `published` = "true" AND `timestamp_published` < NOW() ORDER BY `timestamp_published` DESC ' . (!empty($limit) ? $limit : ''));
    }
}
