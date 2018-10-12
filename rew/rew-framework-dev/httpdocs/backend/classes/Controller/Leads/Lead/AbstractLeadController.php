<?php

namespace REW\Backend\Controller\Leads\Lead;

use REW\Backend\Controller\AbstractController;
use REW\Backend\Exceptions\MissingId\MissingLeadException;
use \Backend_Lead;

/**
 * AbstractFormController
 * @package REW\Backend\Controller\Leads\Lead
 */
abstract class AbstractLeadController extends AbstractController
{

    /**
     * Get Lead From Id
     * @return Backend_Lead
     * @throws MissingLeadException
     */
    public function getLeadFromRequest()
    {
        $leadId = $this->getLeadIdFromRequest();
        $lead = $this->getLeadFromId($leadId);
        $this->validateLead($lead);
        return $lead;
    }

    /**
     * Get Lead Id From Request
     * @return int || null
     */
    public function getLeadIdFromRequest()
    {
        return isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
    }

    /**
     * Get Lead From Id
     * @param int $id
     * @return Backend_Lead
     */
    public function getLeadFromId($id)
    {
        $lead = Backend_Lead::load($id);
        return $lead;
    }

    /**
     * Validate Lead
     * @param \Backend_Lead || null $lead
     * @throws MissingLeadException on missing lead
     * @return bool
     */
    public function validateLead($lead)
    {
        if (empty($lead) || !($lead instanceof Backend_Lead)) {
            throw new MissingLeadException();
        }
        return true;
    }
}
