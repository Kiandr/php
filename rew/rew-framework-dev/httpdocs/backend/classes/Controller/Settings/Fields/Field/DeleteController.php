<?php

namespace REW\Backend\Controller\Settings\Fields\Field;

use REW\Backend\Auth\CustomAuth;
use REW\Backend\Controller\Settings\Fields\Field\AbstractController;
use REW\Backend\Exceptions\PageNotFoundException;
use REW\Backend\Exceptions\SystemErrorException;
use REW\Backend\Interfaces\NoticesCollectionInterface;
use REW\Backend\View\Interfaces\FactoryInterface;
use REW\Core\Interfaces\AuthInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Core\Interfaces\LogInterface;
use REW\Backend\Leads\Interfaces\CustomFieldFactoryInterface;
use REW\Backend\Leads\Interfaces\CustomFieldInterface;

/**
 * DeleteController
 * @package REW\Backend\Controller\Settings\Fields
 */
class DeleteController extends AbstractController
{

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

        // Get Number of Leads with data stored in this field
        $customFieldUsage = $customField->getUsage();

        // Handle request to delete requested page record
        if ($this->checkSubmitting()) {
            try {
                // Delete custom field.
                $this->deleteField($customField);

                // Set success message and save notifications
                $this->notices->success(__('%s has successfully been deleted.', $this->format->htmlspecialchars($customField->getTitle())));

                // Redirect to fields list
                header('Location: ' . URL_BACKEND . 'settings/fields');
                exit;
            } catch (PDOException $e) {
                $this->notices->error(__('Custom field could not be deleted.'));
                $this->log->error($e);
            }
        }

        // Render Custom Fields Header
        echo $this->view->render('::partials/field/summary.tpl.php', [
            'title' => 'Delete ' . $this->format->htmlspecialchars($customField->getTitle()),
            'fieldId' => $this->format->htmlspecialchars($customField->getId()),
            'enabled' => $customField->isEnabled(),
            'customAuth' => $this->customAuth
        ]);

        // Render Edit Custom Field Body
        echo $this->view->render('::pages/settings/fields/field/delete', [
            'id' => $this->format->htmlspecialchars($customField->getId()),
            'customFieldUsage' => $this->format->htmlspecialchars($customFieldUsage)
        ]);
    }

    /**
     * Update Field Data in Database
     * @param CustomFieldInterface $customField
     * @throws PDOException If insert fails
     */
    public function deleteField(CustomFieldInterface $customField)
    {

        // Delete custom field.  Values are deleted via cascade
        $deleteQuery = $this->db->prepare("DELETE FROM `" . $this->getTable() . "` WHERE `id` = :id");
        $deleteQuery->execute(['id' => $customField->getId()]);
    }
}
