<?php

/**
 * History_Event_Delete_LeadTransaction extends History_Event_Delete and is used for tracking when an Agent deletes a lead transaction.
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Delete_LeadTransaction(array(
 *          'type' => 'Buy',
 *          'list_price' => '300000',
 *          'sold_price' => '350000',
 *          'mls_number' => '1234',
 *          'details' => ''
 *     ), array(
 *         new History_User_Agent(1),
 *         new History_User_Lead(1)
 *     ));
 *
 *     $event->save();
 *
 * } catch (Exception $e) {
 *     echo '<p>' . $e->getMessage() . '</p>';
 * }
 * </code>
 *
 * @package History
 */
class History_Event_Delete_LeadTransaction extends History_Event_Delete implements History_IExpandable
{

    /**
     * @see History_Event::getMessage()
     */
    function getMessage(array $options = [])
    {

        // Message View
        $options['view'] = in_array($options['view'], ['system', 'agent', 'lead']) ? $options['view'] : 'system';

        // History Event Users
        foreach ($this->users as $user) {
            $type = $user->getType();
            if (in_array($type, array($user::TYPE_AGENT, $user::TYPE_ASSOCIATE, $user::TYPE_LENDER))) {
                $agent = $user;
            }
            if ($type == $user::TYPE_LEAD) {
                $lead = $user;
            }
        }

        // If Not Set, Make A Dummy Lead/Agent
        if (empty($agent)) {
            $agent = new History_User_Generic(0);
        }
        if (empty($lead)) {
            $lead  = new History_User_Lead(0);
        }

        // Transaction Type
        $type = $this->getData('type');

        // System History
        if ($options['view'] == 'system') {
            return $agent->displayLink() . ' deleted ' . $type . ' transaction for ' . $lead->displayLink();
        }

        // Agent/Associate/Lender History
        if (in_array($options['view'], ['agent'])) {
            return $type . ' transaction deleted for ' . $lead->displayLink();
        }

        // Lead History
        if ($options['view'] == 'lead') {
            return $type . ' transaction deleted by ' . $agent->displayLink();
        }
    }

    /**
     * @see History_IExpandable::getDetails()
     */
    function getDetails()
    {

        $message = $this->getData('details');
        $mls_number = $this->getData('mls_number');
        $message = nl2br($message);

        $pre_message = sprintf('Listing Price: $%s<br>', Format::number($this->getData('list_price')));
        $pre_message .= sprintf('Sold Price: $%s<br>', Format::number($this->getData('sold_price')));
        if (!empty($mls_number)) {
            $pre_message .= sprintf('MLS: %s<br>', $mls_number);
        }

        return $pre_message . 'Details: ' . $message;
    }
}
