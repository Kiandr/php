<?php

namespace REW\Backend\Controller\Leads\Lead;

use REW\Backend\Controller\Leads\Lead\AbstractLeadController;
use REW\Backend\Exceptions\PageNotFoundException;
use REW\Backend\Exceptions\SystemErrorException;
use REW\Backend\Exceptions\MissingIdException;
use REW\Backend\Exceptions\MissingId\MissingLeadException;
use REW\Backend\Exceptions\InvalidActionException;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Backend\Auth\Leads\LeadAuth;
use \Backend_Lead;

/**
 * FormsController
 * @package REW\Backend\Controller\Leads\Lead
 */
class FormsController extends AbstractLeadController
{

    /**
     * Selling Types
     * @var array
     */
    const SELLING_FORMS = ['Seller Form', 'CMA Form', 'Radio Seller Form', 'Guaranteed Sold Form'];

    /**
     * Showing Types
     * @var array
     */
    const SHOWING_FORMS= ['Property Showing', 'Quick Showing'];

    /**
     * Possible Categories
     * @var array
     */
    const CATEGORIES = ['inquiry', 'selling', 'showing'];

    /**
     * Page Limit
     * @var integer
     */
    const PAGE_LIMIT = 25;

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
     * @param NoticesCollectionInterface $notices
     * @param FactoryInterface $view
     * @param AuthInterface $auth
     * @param DBInterface $db
     */
    public function __construct(
        NoticesCollectionInterface $notices,
        FactoryInterface $view,
        AuthInterface $auth,
        DBInterface $db,
        SettingsInterface $settings
    ) {
        $this->notices = $notices;
        $this->view = $view;
        $this->auth = $auth;
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * @throws SystemErrorException If domain not found to manage
     * @throws PageNotFoundException If invalid filter selected
     */
    public function __invoke()
    {

        // Success
        $success = array();

        // Error
        $errors = array();

        // Get Valid Lead
        $this->lead = $this->getLeadFromRequest();

        // Check for Missing Lead Id
        if (empty($this->lead->getRow())) {
            throw new MissingLeadException();
        }

        // Check Lead Authorization
        $this->leadAuth = new LeadAuth($this->settings, $this->auth, $this->lead);
        if (!$this->leadAuth->canViewForms()) {
            throw new UnauthorizedPageException('You do not have permission to view lead forms');
        }

        // Get Request Variables
        $category = $this->getCatgeoryFromRequest();
        $page = $this->getPageFromRequest();
        $delete = $this->getDeleteFromRequest();
        $toggle = $this->getToggleFromRequest();

        // Delete Form
        if (!empty($view)) {
            $form = $this->loadLeadFormFromId($delete);
            if (empty($form)) {
                throw new MissingIdException('A form with the requested ID does not exist');
            }
        }

        // Delete Form
        if (!empty($delete)) {
            try {
                $this->deleteLeadForm($delete);
            } catch (InvalidActionException $e) {
                $errors[]= $e->getMessage();
            } catch (\Exception $e) {
                $errors[]= 'Form could not be deleted.';
            }
        }

        // Toggle Form as Read
        if (!empty($toggle)) {
            try {
                $this->toggleLeadForm($toggle);
            } catch (InvalidActionException $e) {
                $errors []= $e->getMessage();
            } catch (\Exception $e) {
                $errors []= 'Form status could not be changed.';
            }
        }

        // Get Form Count
        $formCount = $this->loadLeadFormCount($category);

        // Get Forms
        if (!empty($formCount)) {
            $formsData = $this->loadLeadForms($formCount, $category, $page);
            $forms = array_map(function ($formData) {
                return $this->parseFormDescription($formData);
            }, $formsData);
        } else {
            $forms = [];
        }

        // Generate Pagination
        list(, $query) = explode('?', $_SERVER['REQUEST_URI'], 2);
        parse_str($query, $queryString);
        $pagination = generate_pagination($formCount, $page, self::PAGE_LIMIT, $queryString);

        // Set notifications
        $this->auth->setNotices($success, $errors);

        // Render template file
        echo $this->view->render('::partials/lead/summary', [
            'title' => 'Lead Forms',
            'lead' => $this->lead,
            'leadAuth' => $this->leadAuth
        ]);

        // Render template file
        echo $this->view->render('::pages/leads/lead/forms', [
            'lead' => $this->lead,
            'forms' => $forms,
            'formCount' => $formCount,
            'categories' => self::CATEGORIES,
            'category' => $category,
            'p' => $page,
            'pagination' => $pagination,
            'canEdit' => $this->leadAuth->canManageLead(),
            'canDelete' => $this->leadAuth->canManageLead()
        ]);
    }

    /**
     * Delete form from user form list
     * @param int $id
     * @throws InvalidActionException
     */
    public function deleteLeadForm($id)
    {

        // Check that agent has permission
        if (!$this->leadAuth->canManageLead()) {
            throw new InvalidActionException('You do not have permissions to delete this leads forms.');
        }

        // Check that form exists
        $formQuery = $this->db->prepare("SELECT `id` FROM `users_forms` WHERE `id` = :id AND `user_id` = :user_id;");
        $formQuery->execute([
            'id' => $id,
            'user_id' => $this->lead->getId()
        ]);
        if ($formQuery->rowCount() === 0) {
            throw new InvalidActionException('The form you are trying to delete no longer exists.');
        }

        // Build DELETE Query
        $deleteQuery = $this->db->prepare("DELETE FROM `users_forms` WHERE `id` = :id AND `user_id` = :user_id;");
        $deleteQuery->execute([
            'id' => $id,
            'user_id' => $this->lead->getId()
        ]);
    }

    /**
     * Toggle Form Read
     * @param int $id
     * @throws InvalidActionException
     */
    public function toggleLeadForm($id)
    {
        // Check that agent has permission
        if (!$this->leadAuth->canManageLead()) {
            throw new InvalidActionException('You do not have permissions to change this forms status.');
        }

        // Check that form exists
        $formQuery = $this->db->prepare("SELECT `id`, `reply` FROM `users_forms` WHERE `id` = :id AND `user_id` = :user_id;");
        $formQuery->execute([
            'id' => $id,
            'user_id' => $this->lead->getId()
        ]);
        $form = $formQuery->fetch();
        if (empty($form)) {
            throw new InvalidActionException('The form you are trying to update no longer exists.');
        }

        // Build Toggle Query
        $newReply = $form['reply'] ? 0 : 1;
        $deleteQuery = $this->db->prepare("Update `users_forms` SET `reply` = :reply WHERE `id` = :id AND `user_id` = :user_id;");
        $deleteQuery->execute([
            'reply' => $newReply,
            'id' => $id,
            'user_id' => $this->lead->getId()
        ]);
    }

    /**
     * Parse Form
     * @param array $formData
     * @return array
     */
    public function parseFormDescription(array $formData)
    {
        $data = unserialize($formData['data']);

        // Get Name
        $name = ($formData['form'] === 'IDX Inquiry' && !empty($data['inquire_type']))
            ? $data['inquire_type']
            : $formData['form'];

        // Get Address
        $address = (!empty($data['address']))
            ? $address = $data['address']
            : implode(', ', array_filter([$data['fm-addr'], $data['fm-town'], $data['fm-state'], $data['fm-postcode']]));

        // Get Comments
        $comments = $data['comments'];
        if (empty($comments)) {
            $comments = $data['inquire']['comments'];
        }
        if (empty($comments)) {
            $comments = $data['showing']['comments'];
        }

        return [
            'id' => $formData['id'],
            'name' => $name,
            'address' => $address,
            'comments' => $comments,
            'reply' => $formData['reply'],
            'read' => $formData['read'],
            'timestamp' => strtotime($formData['timestamp'])
        ];
    }

    /**
     * Get Lead Forms
     * @param int $count
     * @param stirng $category
     * @param itn $page
     * @return array
     */
    public function loadLeadForms($count, $category, $page)
    {

        // Build Query
        $whereQuery = $this->getWhereQuery($category);
        $limitQuery = ($count > self::PAGE_LIMIT) ? $this->getLimitQuery($page, loadLeadForms) : '';
        $orderQuery = $this->getOrderQuery();
        $leadFormQuery = 'SELECT * FROM `users_forms`' . $whereQuery . $orderQuery. $limitQuery;

        // Build Params
        $leadFormParams = $this->getWhereParams($category);

        $leadFormQuery = $this->db->prepare($leadFormQuery);
        $leadFormQuery->execute($leadFormParams);
        return $leadFormQuery->fetchAll();
    }

    /**
     * Get Lead Form Count
     * @param string $category
     * @return int
     */
    public function loadLeadFormCount($category)
    {

        // Build Query
        $whereQuery = $this->getWhereQuery($category);
        $leadFormCountQuery = 'SELECT COUNT(*) FROM `users_forms`' . $whereQuery;

        // Build Params
        $leadFormCountParams = $this->getWhereParams($category);

        $leadFormCountQuery = $this->db->prepare($leadFormCountQuery);
        $leadFormCountQuery->execute($leadFormCountParams);
        return $leadFormCountQuery->fetchColumn();
    }

    /**
     * Get Category from Request
     * @return string|null
     */
    public function getCatgeoryFromRequest()
    {
        return $_GET['category'];
    }

    /**
     * Get Category from Request
     * @return string|null
     */
    public function getPageFromRequest()
    {
        return $_GET['p'];
    }

    /**
     * Get Delete from Request
     * @return int|null
     */
    public function getDeleteFromRequest()
    {
        return !empty($_POST['delete']) ? intval($_POST['delete']) : null;
    }

    /**
     * Get Toggle from Request
     * @return int|null
     */
    public function getToggleFromRequest()
    {
        return !empty($_GET['toggle']) ? intval($_GET['toggle']) : null;
    }

    /**
     * Get Where String
     * @param string $category
     * @return string
     */
    public function getWhereQuery($category = null)
    {
        $sql_where = ' WHERE `user_id` = ?';
        if (!empty($category)) {
            $sql_category = [];

            if ($category == 'inquiry') {
                $sql_where .= ' AND ' . $this->getInquiryFormQuery();
            } else if ($category == 'selling') {
                $sql_where .= ' AND ' . $this->getSellingFormQuery();
            } else if ($category == 'showing') {
                $sql_where .= ' AND ' . $this->getShowingFormQuery();
            }
        }
        return $sql_where;
    }

    /**
     * Get Order Query
     * @return string
     */
    public function getOrderQuery()
    {
        return " ORDER BY `timestamp` DESC";
    }

    /**
     * Get Limit Query
     * @param int $page
     * @return string
     */
    public function getLimitQuery($page)
    {
        // Search Limit
        $limitvalue = (($page- 1) * PAGE_LIMIT);
        $limitvalue = ($limitvalue > 0) ? $limitvalue : 0;
        $sql_limit = " LIMIT " . $limitvalue . "," . self::PAGE_LIMIT;
        return $sql_limit;
    }

    /**
     * Get Where Parameters
     * @param string $category
     * @return array
     */
    public function getWhereParams($category = null)
    {
        $sql_where_params = [$this->lead->getId()];
        if (!empty($category)) {
            if ($category == 'inquiry') {
                $sql_where_params = array_merge($sql_where_params, array_merge(self::SELLING_FORMS, self::SHOWING_FORMS));
            } else if ($category == 'selling') {
                $sql_where_params = array_merge($sql_where_params, self::SELLING_FORMS);
            } else if ($category == 'showing') {
                $sql_where_params = array_merge($sql_where_params, self::SHOWING_FORMS);
            }
        }
        return $sql_where_params;
    }

    /**
     * Get Form Query Strings
     * @return string
     */
    protected function getInquiryFormQuery()
    {
        return "(`form` NOT IN (" . implode(', ', array_fill(0, count(array_merge(self::SELLING_FORMS, self::SHOWING_FORMS)), '?')) . ")"
            . " AND (`form` != 'IDX Inquiry'"
            . " OR (`data` NOT LIKE '%s:12:\"inquire_type\";s:16:\"Property Showing\";%' AND `data` NOT LIKE '%s:12:\"inquire_type\";s:7:\"Selling\";%')))";
    }

    /**
     * Get Form Query Strings
     * @return string
     */
    protected function getSellingFormQuery()
    {
        return "(`form` IN (" . implode(', ', array_fill(0, count(self::SELLING_FORMS), '?')) . ")"
            . " OR (`form` = 'IDX Inquiry'"
            . " AND `data` LIKE '%s:12:\"inquire_type\";s:7:\"Selling\";%'))";
    }

    /**
     * Get Form Query Strings
     * @return string
     */
    protected function getShowingFormQuery()
    {
        return "(`form` IN (" . implode(', ', array_fill(0, count(self::SHOWING_FORMS), '?')) . ")"
            . " OR (`form` = 'IDX Inquiry'"
            . " AND `data` LIKE '%s:12:\"inquire_type\";s:16:\"Property Showing\";%'))";
    }
}
