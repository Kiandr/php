<?php

/**
 * Hook_REW_OutgoingAPI_Lead_FormSubmission
 *
 * @package Hooks
 */
class Hook_REW_OutgoingAPI_Lead_FormSubmission extends Hook_REW_OutgoingAPI
{

    /**
     * Run the hook's code
     * @param array $post The form's submitted fields & values
     * @param string $form_name The name of the form being submitted
     * @param array $lead The lead's row from the database
     * @param array $listing The listing row associated with the submission (if any)
     * @return array $post
     */
    protected function invoke($post, $form_name, $lead, $listing = null)
    {
        $this->sendOutgoingEvent($this->getName(), array(
            'form_name' => $form_name,
            'lead'      => $lead,
            'post'      => $post,
            'listing'   => $listing,
        ));
        return $post;
    }
}
