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
use \InvalidArgumentException;

/**
 * DisableController
 * @package REW\Backend\Controller\Settings\Fields
 */
class DisableController extends AbstractController
{

    /**
     * Type Field
     * @var string
     */
    const TYPE_ENABLED = 'enabled';

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
        $this->log = $format;
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

        // Handle request to disable field
        if ($this->checkSubmitting()) {
            try {
                if (!$customField->isEnabled()) {
                    throw new InvalidArgumentException(__('This field has already been disabled.'));
                }

                // Create Insert Query
                $enableQuery = $this->db->prepare("UPDATE `" . $this->getTable(). "` SET `enabled`  = 0 WHERE `id` = :id");
                $enableQuery->execute(['id' => $customField->getId()]);

                // Set success message and save notifications
                $this->notices->success(__('%s has successfully been disabled.', $customField->getTitle()));

                // Redirect to fields list
                header('Location: ' . URL_BACKEND . 'settings/fields');
                exit;
            } catch (PDOException $e) {
                $this->notices->error(__('Custom field could not be disabled.'));
                $this->log->error($e);
            } catch (InvalidArgumentException$e) {
                $this->notices->error($e->getMessage());
                $this->log->error($e);
            }
        }

        // Render Custom Fields Header
        echo $this->view->render('::partials/field/summary.tpl.php', [
            'title' => 'Disable ' . $this->format->htmlspecialchars($customField->getTitle()),
            'fieldId' => $this->format->htmlspecialchars($customField->getId()),
            'enabled' => $customField->isEnabled(),
            'customAuth' => $this->customAuth
        ]);

        // Render Edit Custom Field Body
        echo $this->view->render('::pages/settings/fields/field/disable', [
            'id' => $this->format->htmlspecialchars($customField->getId()),
            'enabled' => $customField->isEnabled()
        ]);
    }

    /**
     * Update Field Data in Database
     * @param CustomFieldInterface $customField
     * @throws InvalidArgumentException If already enabled
     * @throws PDOException If insert fails
     */
    public function disableField(CustomFieldInterface $customField)
    {

        if (!$customField->isEnabled()) {
            throw new InvalidArgumentException(__('This field has already been enabled.'));
        }

        // Create Update Query
        $enableQuery = $this->db->prepare("UPDATE `" . $this->getTable(). "` SET `enabled`  = 0 WHERE `id` = :id");
        $enableQuery->execute(['id' => $customField->getId()]);
    }
}
