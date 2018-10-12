<?php

/**
 * Hook_REW_FollowUpBoss_FormSubmission
 * Notifies Follow Up Boss when form submissions occur
 *
 * @package Hooks
 */
class Hook_REW_FollowUpBoss_Lead_FormSubmission extends Hook_REW_FollowUpBoss
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

        // Require partner
        if (!($fub = $this->getPartner())) {
            return;
        }

        // Check form type
        switch ($form_name) {
            case 'IDX Registration':
                $fub->notifyRegistration($lead['id'], !empty($listing) ? $listing : array());
                break;
            case 'IDX Inquiry':
            case 'Quick Inquire':
            case 'Quick Showing':
                // Inquiry type
                $fub_inquire_type = !empty($post['inquire_type']) ? $post['inquire_type'] : 'Property Inquiry';

                // User comments
                $fub_inquire_comments = null;
                if (!empty($post['inquire']['comments'])) {
                    $fub_inquire_comments = $post['inquire']['comments'];
                } else if (!empty($post['showing']['comments'])) {
                    $fub_inquire_comments = $post['showing']['comments'];
                } else {
                    $fub_inquire_comments = $post['comments'];
                }

                // Require inquiry comments
                if (empty($fub_inquire_comments)) {
                    break;
                }

                // Notify FUB
                $fub->notifyPropertyInquiry($lead['id'], $listing, $fub_inquire_type, $fub_inquire_comments);

                break;
            default:
                // Require form comments
                if (empty($post['comments'])) {
                    break;
                }

                // Form subject
                $subject = $form_name . (!empty($_SERVER['HTTP_REFERER']) ? ' at ' . $_SERVER['HTTP_REFERER'] : '');
                $fub->notifyGeneralInquiry($lead['id'], $subject, $post['comments']);

                break;
        }
        return $post;
    }
}
