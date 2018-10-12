<?php

namespace REW\Backend\Controller\Settings\Fields;

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
use \InvalidArgumentException;

/**
 * AddController
 * @package REW\Backend\Controller\Settings\Fields
 */
class AddController extends AbstractController
{

    /**
     * Title Name
     * @var string
     */
    const TITLE_FIELD = 'title';

    /**
     * Type Field
     * @var string
     */
    const TYPE_FIELD = 'type';

    /**
     * Required Fields
     * @var array
     */
    const REQUIRED_FIELDS = ['title', 'type'];

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
     * @param FormatInterface $format
     * @param LogInterface $log
     * @param CustomFieldFactory $customFieldFactory
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

        // Check Authoization
        $this->canManageFields();

        // Handle request to delete requested page record
        if ($this->checkSubmitting()) {
            try {
                // Get field data to insert
                $fieldData = $this->getFieldData();

                // Save field data
                $this->saveFieldData($fieldData);

                // Set success message and save notifications
                $this->notices->success(__('Custom field has successfully been created.'));

                // Redirect to new field's edit page
                header('Location: ' . URL_BACKEND . 'settings/fields/field/edit?id=' . $this->db->lastInsertId());
                exit;
            } catch (PDOException $e) {
                $this->notices->error(__('Custom field could not be added.'));
                $this->log->error($e);
            } catch (InvalidArgumentException $e) {
                $this->notices->error($e->getMessage());
                $this->log->error($e);
            } catch (MissingParameterException $e) {
                $this->notices->error($e->getMessage());
                $this->log->error($e);
            }
        }

        // Render template file
        echo $this->view->render('::pages/settings/fields/add', [
            'title' => $this->format->htmlspecialchars($fieldData['title']),
            'currentType' => $this->format->htmlspecialchars($fieldData['type']),
            'types' => $this->getTypes()
        ]);
    }

    /**
     * Insert Field Data into Database
     * @param array
     * @throws PDOException If insert fails
     */
    public function saveFieldData(array $fieldData)
    {

        // Create Insert Query
        $insertQuery = $this->db->prepare("INSERT INTO `" . $this->getTable() . "` SET "
            . "`name`    = :name, "
            . "`title`   = :title, "
            . "`type`    = :type, "
            . "`enabled` = 1");

        $insertParams = [
            'name'  => $fieldData['name'],
            'title' => $fieldData['title'],
            'type'  => $fieldData['type'],
        ];

        $insertQuery->execute($insertParams);
    }

    /**
     * Get data to be inserted
     * @returns array
     * @throws MissingParameterException If a required parameter is missing
     * @throws InvalidArgumentException If invalid data is not thrown
     */
    public function getFieldData()
    {

        // Get user data for post fields
        $postData = $this->getPostData();

        // Check & Parse data for valid insert parameters
        $this->checkRequiredFields($postData);
        $this->checkTypeField($postData['type']);
        $name = $this->format->slugify($postData['title']);
        $this->checkDuplicateName($name);
        $postData['name'] = $name;
        return $postData;
    }

    /**
     * Check Required Fields are in data set
     * @throws MissingParameterException If a required parameter is missing
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
    public function checkDuplicateName($name)
    {

        // Check for existing field
        $checkQuery = $this->db->prepare(
            "SELECT `id` FROM `" . $this->getTable(). "` WHERE `name` = :name"
        );
        $checkQuery->execute(['name' => $name]);
        if ($checkQuery->rowCount()) {
            throw new InvalidArgumentException(__('%s is already a custom field.', ucfirst(strtolower($name))));
        }
    }
}
