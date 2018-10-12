<?php

use REW\Core\Interfaces\DBInterface;

/**
 * History_Event_Action_FormSubmission extends History_Event_Action and is used for tracking when a Lead has filled out a form submission
 *
 * <code>
 * try {
 *
 *     $event = new History_Event_Action_FormSubmission(array(
 *         'page' => 'http://www.example.com/contact.php',
 *         'form' => 'Contact Form',
 *         'data' => $_POST
 *     ), array(
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
class History_Event_Action_FormSubmission extends History_Event_Action implements History_IExpandable
{

    /* Basic Message */
    function getMessage(array $options = array())
    {

        /* Message View */
        $options['view'] = in_array($options['view'], array('system', 'lead')) ? $options['view'] : 'system';

        /* History Event Users */
        foreach ($this->users as $user) {
            $type = get_class($user);
            if ($type == 'History_User_Lead') {
                $lead = $user;
            }
        }

        // If Not Set, Make A Dummy Lead
        if (empty($lead)) {
            $lead = new History_User_Lead(0);
        }

        /* System Message */
        if ($options['view'] == 'system') {
            return 'Form Submission by ' . $lead->displayLink() . ': ' . $this->getData('form');
        }

        /* Lead Message */
        if ($options['view'] == 'lead') {
            return 'Form Submission: ' . $this->getData('form');
        }
    }

    /* Extra Details */
    function getDetails()
    {

        /* Form Data */
        $data = $this->getData('data');

        /* Is String? */
        if (is_string($data)) {
            return $data;
        }

        /* Is Array? */
        if (is_array($data)) {
            /* HTML Output */
            $output = array();

            /* Re-Map Honeypot Variables */
            if (isset($data['mi0moecs']) ||
                isset($data['onc5khko']) ||
                isset($data['sk5tyelo'])) {
                $data['first_name'] = $data['onc5khko'];
                $data['last_name']  = $data['sk5tyelo'];
                $data['email']      = $data['mi0moecs'];
                unset($data['onc5khko'], $data['sk5tyelo'], $data['mi0moecs']);
            }

            if ((isset($data['telephone']) || isset($data['fm-mobile']))) {
                $data['primary_phone'] = $data['telephone'];
                $data['secondary_phone'] = $data['fm-mobile'];
                unset($data['telephone'], $data['fm-mobile']);
            }


            /* Ignore Fields */
            $ignore = array('reset', 'send', 'step', 'submit');

            /* Build HTML */
            foreach ($data as $k => $v) {
                /* Skip Ignored Fields */
                foreach ($ignore as $i) {
                    if (strtolower($i) == strtolower($k)) {
                        continue 2;
                    }
                }

                /* Implode Array to String */
                if (is_array($v)) {
                    $v = implode(', ', $v);
                }

                /* Trim Data */
                $v = trim($v);

                /* Skip Empty */
                if (empty($v)) {
                    continue;
                }

                // Valid URL - Display as anchor link
                $v = (filter_var($v, FILTER_VALIDATE_URL) ? '<a href="' . $v . '" target="_blank">' . urldecode($v) . '</a>' : nl2br(htmlspecialchars($v)));

                /* Append HTML */
                $output[] = '<strong>' . ucwords(htmlspecialchars(str_replace('_', ' ', $k))) . ':</strong> ' . $v;
            }

            /* Form Referer */
            $page = $this->getData('page');
            $page = str_replace(array('/inquire/', '/friend/'), '/', $page);
            $page = preg_replace('/(\\?|&)uid=([A-Za-z0-9]{40})/', '', $page);
            $page = preg_replace('/(\\?|&)facebox_Frame(=(true|false))?/', '', $page);
            $page = preg_replace('/(\\?|&)popup/', '', $page);
            $page = trim($page, '?');

            /* Return HTML */
            return ''
                 . '<p>This form was submitted from: <a href="' . $page . '" target="_blank">' . $page . '</a></p>'
                 . '<ul><li>' . implode('</li><li>', $output) . '</li></ul>';
        }
    }

    /**
     * Save Event Data
     * @see History_Event::save()
     */
    public function save(DBInterface $db = null)
    {

        // Get DB Connection
        $db = is_null($db) ? $this->db : $db;

        // Save as Usual
        if (parent::save($db)) {
            // Last Form Data
            $data = array('timestamp' => $this->getTimestamp(), 'type' => $this->getData('form'));

            // Increment `user`.`num_forms`
            foreach ($this->users as $user) {
                if ($user instanceof History_User_Lead) {
                    $db->query("UPDATE `users` SET `num_forms` = `num_forms` + 1, `last_form` = '" . json_encode($data) . "' WHERE `id` = '" . $user->getUser() . "';");
                }
            }

            // Return Event ID
            return $this->id;
        }

        // Return False
        return false;
    }
}
