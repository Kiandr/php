<?php

namespace REW\Backend\Controller\Settings\Fields\Field;

use REW\Backend\Auth\CustomAuth;
use REW\Backend\Controller\Settings\Fields\Field\AbstractController;
use REW\Backend\Exceptions\PageNotFoundException;
use REW\Backend\Exceptions\MissingParameterException;
use REW\Backend\Exceptions\SystemErrorException;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Backend\Leads\Interfaces\CustomFieldFactoryInterface;
use REW\Backend\Leads\Interfaces\CustomFieldInterface;
use \InvalidArgumentException;

/**
 * EditController
 * @package REW\Backend\Controller\Settings\Fields
 */
class EditController extends AbstractController
{

    /**
     * Required Fields
     * @var array
     */
    const REQUIRED_FIELDS = ['title', 'type', 'enabled'];

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
     * @param CustomAuth $customAuth
     * @param NoticesCollectionInterface $notices
     * @param FactoryInterface $view
     * @param AuthInterface $auth
     * @param DBInterface $db
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
        $this->customFieldFactory = $customFieldFactory;
    }

    /**
     * @throws SystemErrorException If domain not found to manage
     * @throws PageNotFoundException If invalid filter selected
     */
    public function __invoke()
    {

        // Check Authoization
        $this->canManageFields();

        // Load custom field
        $customField = $this->getCustomField();

        // Handle request to delete requested page record
        if ($this->checkSubmitting()) {
            try {
                // Get field data to insert
                $fieldData = $this->getFieldData($customField->getId());

                // Save field data
                $this->saveFieldData($customField, $fieldData);

                // Set success message and save notifications
                $this->notices->success(__('%s has successfully been updated.', $this->format->htmlspecialchars($fieldData['title'])));

                // Redirect to new fresh edit page
                header('Location: ' . URL_BACKEND . 'settings/fields/field/edit?id=' . $customField->getId());
                exit;
            } catch (PDOException $e) {
                $this->notices->error(__('Custom field could not be updated.'));
                $this->log->error($e);
            } catch (InvalidArgumentException$e) {
                $this->notices->error($e->getMessage());
                $this->log->error($e);
            } catch (MissingParameterException $e) {
                $this->notices->error($e->getMessage());
                $this->log->error($e);
            }
        }

        // Render Custom Fields Header
        echo $this->view->render('::partials/field/summary.tpl.php', [
            'title' => __('Edit %s', $customField->getTitle()),
            'fieldId' => $customField->getId(),
            'enabled' => $customField->isEnabled(),
            'customAuth' => $this->customAuth
        ]);

        // Render Edit Custom Field Body
        echo $this->view->render('::pages/settings/fields/field/edit', [
            'id' => $customField->getId(),
            'title' => $customField->getTitle(),
            'enabled' => $customField->isEnabled(),
            'currentType' => $customField->getType(),
            'types' => $this->getTypes()
        ]);
    }

    /**
     * Update Field Data in Database
     * @param array
     * @throws PDOException If insert fails
     */
    public function saveFieldData(CustomFieldInterface $customField, $fieldData)
    {

        // Create Update Query
        $updateQuery = $this->db->prepare("UPDATE `" . $this->getTable() . "` SET "
            . "`name`    = :name, "
            . "`title`   = :title, "
            . "`type`    = :type, "
            . "`enabled` = :enabled"
            . " WHERE `id` = :id");

        $updateParams = [
            'name'    => $fieldData['name'],
            'title'   => $fieldData['title'],
            'type'    => $fieldData['type'],
            'enabled' => $fieldData['enabled'],
            'id'      => $customField->getId()
        ];

        $updateQuery->execute($updateParams);
    }

    /**
     * Get data to be inserted
     * @param int $id
     * @returns array
     * @throws MissingParameterException If a required parameter is missing
     * @throws InvalidArgumentException If invalid data is not thrown
     */
    public function getFieldData($id)
    {

        // Get user data for post fields
        $postData = $this->getPostData();

        // Check & Parse data for valid insert parameters
        $this->checkRequiredFields($postData);
        $this->checkTypeField($postData['type']);
        $name = $this->format->slugify($postData['title']);
        $this->checkDuplicateName($id, $name);
        $postData['name'] = $name;
        return $postData;
    }

    /**
     * Check Required Fields are in data set
     * @throws UnauthorizedPageException If a required parameter is missing
     */
    public function checkRequiredFields(array $data)
    {
        foreach (self::REQUIRED_FIELDS as $requiredField) {
            if (!isset($data[$requiredField])) {
                throw new MissingParameterException(__('%s is a required field.', ucfirst(strtolower($requiredField))));
            }
        }
    }

    /**
     * Check Type field is a valid type
     * @param string $type
     * @throws InvalidArgumentException
     */
    public function checkTypeField($type)
    {
        if (!in_array($type, array_keys($this->getTypes()))) {
            throw new InvalidArgumentException(__('%s is an invalid type.', ucfirst(strtolower($type))));
        }
    }

    /**
     * Check for duplicate names
     * @param string $type
     * @throws InvalidArgumentException
     * @throws PDOException
     */
    public function checkDuplicateName($id, $name)
    {

        // Check for existing field
        $checkQuery = $this->db->prepare("SELECT `id` FROM `" . $this->getTable(). "` WHERE `id` != :id && `name` = :name");
        $checkQuery->execute(['id' => $id, 'name' => $name]);
        if ($checkQuery->rowCount()) {
            throw new InvalidArgumentException(__('%s is already another custom field.', ucfirst(strtolower($name))));
        }
    }
}
