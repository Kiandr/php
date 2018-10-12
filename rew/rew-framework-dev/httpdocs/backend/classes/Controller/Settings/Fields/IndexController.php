<?php

namespace REW\Backend\Controller\Settings\Fields;

use REW\Backend\Auth\CustomAuth;
use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\PageNotFoundException;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Exceptions\SystemErrorException;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Backend\Leads\Interfaces\CustomFieldFactoryInterface;

/**
 * IndexController
 * @package REW\Backend\Controller\Settings\Fields
 */
class IndexController extends AbstractController
{

    /**
     * User Fields Table
     * @var string
     */
    const FIELDS_TABLE = 'users_fields';

    /**
     * Filter: All Custom Fields
     * @var string
     */
    const FILTER_ALL = 'all';

    /**
     * Filter: Enabled Fields
     * @var string
     */
    const FILTER_ENABLED = 'enabled';

    /**
     * Filter: Disabled Fields
     * @var string
     */
    const FILTER_DISABLED = 'disabled';

    /**
     * Default filter
     * @var string
     */
    const DEFAULT_FILTER = self::FILTER_ALL;

    /**
     * @var CustomAuth
     */
    protected $customAuth;

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
     * @var DBInterface
     */
    protected $db;

    /**
     * @var FormatInterface
     */
    protected $format;

    /**
     * @var LogInterface
     */
    protected $log;

    /**
     * @var CustomFieldFactoryInterface
     */
    protected $customFieldFactory;

    /**
     * @param CustomAuth $customAuth
     * @param NoticesCollectionInterface $notices
     * @param FactoryInterface $view
     * @param AuthInterface $auth
     * @param DBInterface $db
     * @param FormatInterface $format
     * @param LogInterface $log
     * @param CustomFieldFactoryInterface $customFieldFactory
     */
    public function __construct(
        CustomAuth $customAuth,
        NoticesCollectionInterface $notices,
        FactoryInterface $view,
        AuthInterface $auth,
        DBInterface $db,
        FormatInterface $format,
        LogInterface $log,
        CustomFieldFactoryInterface $customFieldFactory
    ) {
        $this->customAuth = $customAuth;
        $this->notices = $notices;
        $this->view = $view;
        $this->auth = $auth;
        $this->db = $db;
        $this->format = $format;
        $this->log = $log;
        $this->customFieldFactory= $customFieldFactory;
    }

    /**
     * @throws SystemErrorException If domain not found to manage
     * @throws PageNotFoundException If invalid filter selected
     */
    public function __invoke()
    {

        // Check Authorization
        $this->canManageFields();

        // Get current field filter
        $filter = $this->getFilter();
        $filters = $this->getFilters();

        // Invalid field filter selected
        if (!isset($filters[$filter])) {
            throw new PageNotFoundException;
        }

        // Fetch and format fields for current filter
        $fields = $this->formatFields($this->getFields($filter));

        // Render template file
        echo $this->view->render('::pages/settings/fields/default', [
            'canDelete' => $this->customAuth->canDeleteFields(),
            'filters' => $filters,
            'filter' => $filter,
            'fields' => $fields
        ]);
    }

    /**
     * Authorized to Manage Custom Fields
     * @throws UnauthorizedPageException
     */
    public function canManageFields()
    {
        if (!$this->customAuth->canManageFields()) {
            throw new UnauthorizedPageException(__('You do not have permission to manage custom fields.'));
        }
    }

    /**
     * Get current field filter
     * @return string
     */
    public function getFilter()
    {
        return $_GET['filter'] ?: self::DEFAULT_FILTER;
    }

    /**
     * Get available field filters
     * @return array
     */
    public function getFilters()
    {
        return [
            self::FILTER_ALL => __('All Fields'),
            self::FILTER_ENABLED => __('Enabled Fields'),
            self::FILTER_DISABLED => __('Disabled Fields')
        ];
    }

    /**
     * Get filtered array of custom fields
     * @param string $filter
     * @return array
     */
    public function getFields($filter)
    {
        if ($filter === self::FILTER_ALL) {
            return $this->getAllFields($subdomain);
        } elseif ($filter === self::FILTER_ENABLED) {
            return $this->getEnabledFields($subdomain);
        } elseif ($filter === self::FILTER_DISABLED) {
            return $this->getDisabledFields($subdomain);
        }
        return [];
    }

    /**
     * Get all fields
     * @return array
     */
    public function getAllFields()
    {
        return $this->customFieldFactory->loadCustomFields();
    }

    /**
     * Get enabled fields
     * @return array
     */
    public function getEnabledFields()
    {
        return $this->customFieldFactory->loadEnabledCustomFields();
    }

    /**
     * Get disabled fields
     * @return array
     */
    public function getDisabledFields()
    {
        return $this->customFieldFactory->loadDisabledCustomFields();
    }

    /**
     * Parse Arrays for Template
     * @param CustomFieldInterface[] $fields
     * @return array
     */
    public function formatFields(array $fields)
    {

        return array_map(function ($field) {
            return [
                'id' => $this->format->htmlspecialchars($field->getId()),
                'title' => $this->format->htmlspecialchars($field->getTitle()),
                'type' => ucfirst(strtolower($this->format->htmlspecialchars($field->getType()))),
                'enabled' => $field->isEnabled()
            ];
        }, $fields);
    }
}
