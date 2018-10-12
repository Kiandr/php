<?php

namespace REW\Backend\Controller\Settings\Fields\Field;

use REW\Backend\Controller\AbstractController as ControllerAbstractController;
use REW\Backend\Auth\CustomAuth;
use REW\Backend\Leads\Interfaces\CustomFieldFactoryInterface;
use REW\Backend\Leads\Interfaces\CustomFieldInterface;

use REW\Backend\Exceptions\SystemErrorException;
use REW\Backend\Exceptions\UnauthorizedPageException;
use REW\Backend\Exceptions\MissingId\MissingCustomFieldException;

/**
 * AbstractControllergetCustomField
 * @package REW\Backend\Controller
 */
abstract class AbstractController extends ControllerAbstractController
{


    /**
     * @var CustomAuth
     */
    protected $customAuth;

    /**
     * @var CustomFieldFactoryInterface
     */
    protected $customFieldFactory;

    /**
     * Authorized to Manage Custom Fields
     * @throws UnauthorizedPageException
     */
    public function canManageFields()
    {
        if (!$this->customAuth->canManageFields()) {
            throw new UnauthorizedPageException(
                __('You do not have permission to manage custom fields.')
            );
        }
    }

    /**
     * Get valid types
     * @return array
     */
    public function getTable()
    {
        return $this->customFieldFactory->getTable();
    }

    /**
     * Get valid types
     * @return array
     */
    public function getTypes()
    {
        return $this->customFieldFactory->getTypes();
    }

    /**
     * Is the form being submitted
     * @return boolean
     */
    public function checkSubmitting()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Get Id from Super Globals
     * @return int|NULL
     */
    public function getId()
    {
        return isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
    }

    /**
     * Get Custom Field Array from Superglobals
     * @returns CustomFieldInterface
     * @throws MissingCustomFieldException
     */
    public function getCustomField()
    {

        // Get Id to be enabled
        $id = $this->getId();
        if (empty($id)) {
            throw MissingCustomFieldException();
        }

        // Load Custom Field from Id
        try {
            $customField = $this->customFieldFactory->loadCustomField($id);
            if (!$customField) {
                throw MissingCustomFieldException();
            }
            return $customField;
        } catch (\PDOException $e) {
            throw new SystemErrorException(
                __('An error occurred while loading custom field.')
            );
        }
    }

    /**
     * Get Post Data
     * @return boolean
     */
    public function getPostData()
    {
        return $_POST;
    }
}
