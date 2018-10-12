<?php

/**
 * Hook_REW_Core_Lead_FormSubmission
 * This hook is invoked on lead form submissions to track seller leads
 * @package Hooks
 */
class Hook_REW_Core_Lead_FormSubmission extends Hook_REW_OutgoingAPI
{

    /**
     * Name of Seller Group
     * @var string
     */
    const SELLER_GROUP_NAME = 'Seller';

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
        try {
            // Seller lead information
            $user = User_Session::get();
            $seller = $user->info('seller');
            if (!empty($seller)) {
                // Seller details
                $merge = array_filter(array(
                    'Seller Details' => array(
                        'Address'       => $seller['location'],
                        'House Type'    => $seller['criteria']['type'],
                        'Sub-Type'      => $seller['criteria']['subtype'],
                        'Bedrooms'      => $seller['criteria']['bedrooms'],
                        'Bathrooms'     => $seller['criteria']['bathrooms'],
                        'Sq. Ft.'       => $seller['criteria']['sqft'],
                        'Condition'     => $seller['criteria']['condition']
                    )
                ));

                // Merge seller data into POST
                $post = array_merge($post, $merge);

                // Load Lead
                $lead = new Backend_Lead($lead);

                // Find & assign this lead to the  'Seller Leads' group
                $result = DB::get()->prepare("SELECT * FROM `groups` WHERE `name` = :group;");
                $result->execute(array('group' => self::SELLER_GROUP_NAME));
                $group = $result->fetch();
                if (!empty($group)) {
                    $lead->assignGroup($group);
                }

                // Clear seller details
                $user->saveInfo('seller', false);
            }

        // Error occurred
        } catch (Exception $e) {
            //Log::error($e->getMessage());
        }

        // Return POST
        return $post;
    }
}
