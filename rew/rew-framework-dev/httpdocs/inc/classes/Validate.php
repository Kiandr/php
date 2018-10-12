<?php

use REW\Core\Interfaces\EnvironmentInterface;

/**
 * Validate
 *
 */
class Validate
{

    /**
     * Validate email address by checking its syntax to see if it is formatted correctly
     *
     * @param string $email
     * @return bool
     */
    public static function email($email, $syntax_only = false, &$status = null)
    {
        $_timer = Profile::timer()->stopwatch(__METHOD__)->start();
        $validator = new Validate_Email;

        // Only Checking syntax as port 25 is blocked on GCE servers.
        $validator->check($email, true);
        $status = $validator->getStatus();
        $valid = ($validator->getStatus() == EVSTATUS_SYNTAX_OK);

        $_timer->setDetails($email . ':' . ($valid ? '1' : '0'))->stop();
        return $valid;
    }

    /**
     * Check if the specified e-mail host is set to always require verification
     * (Server-wide setting)
     *
     * @param string $email Full e-mail address
     * @return boolean
     */
    public static function verifyRequired($email)
    {

        // Extract domain name
        list($account, $domain) = explode('@', $email);
        $domain = strtolower($domain);

        // Require domain name
        if (empty($domain)) {
            return false;
        }

        // Get core settings
        $settings = Container::getInstance()->get(EnvironmentInterface::class)->loadMailCRMSettings()
            ->getCoreConfig();

        // Read whitelist
        if (@in_array($domain, $settings['verify_required'])) {
            return true;
        }

        // Verify not required for this domain
        return false;
    }

    /**
     * Check if the specified e-mail host is set to skip verification
     * (Server-wide setting)
     *
     * @param string $email Full e-mail address
     * @return boolean
     */
    public static function verifyWhitelisted($email)
    {

        // Extract domain name
        list($account, $domain) = explode('@', $email);
        $domain = strtolower($domain);

        // Require domain name
        if (empty($domain)) {
            return false;
        }

        // Get core settings
        $settings = Container::getInstance()->get(EnvironmentInterface::class)->getCoreConfig();

        // Read whitelist
        if (@in_array($domain, $settings['blacklisted'])) {
            return true;
        }

        // Verify not whitelisted for this domain
        return false;
    }

    /**
     * Validate that a sting contains at least 7 digits by default
     * Strict mode matches only ###-###-####
     *
     * @param string $phone
     * @param bool $strict
     * @return bool
     */
    public static function phone($phone, $strict = false)
    {
        if ($strict) {
            return preg_match('/([0-9]{3})-([0-9]{3})-([0-9]{4})/', $phone) ? true : false;
        } else {
            $phone_test = preg_match_all('/(\d)/', $phone, $matches);
            if (preg_match('/([a-zA-Z])/', $phone)) {
                return false;
            }
            return $phone_test >= 7 ? true : false;
        }
    }

    /**
     * Verify that the string is not empty
     *
     * @param string $str
     * @return bool
     */
    public static function stringRequired($str)
    {
        $str = trim($str);
        return strlen($str) ? true : false;
    }

    /**
     * Checks list of provided fields for existence of disallowed characters and returns
     * a list of the not allowed characters and a formatted list of fields that did not pass.
     * @param associative array of strings $check_fields
     * @return multitype:multitype:string  string
     */
    public static function formFields($check_fields)
    {
        //    Not An Array           OR  Is Not An Associative Array
        if (!is_array($check_fields) || !(array_keys($check_fields) !== range(0, count($check_fields) - 1))) {
            throw new UnexpectedValueException('An associative array of fields to be checked is required');
        }

        $bad_fields = array ();

        $not_allowed = array ('<', '>', '"', '?', ':', '*');
        foreach ($check_fields as $field => $value) {
            if (!empty($value) && strcmp($value, str_replace($not_allowed, '', $value)) != 0) {
                switch ($field) {
                    case 'onc5khko':
                    case 'first_name':
                        $bad_fields[$field] = 'First Name';
                        break;
                    case 'sk5tyelo':
                    case 'last_name':
                        $bad_fields[$field] = 'Last Name';
                        break;
                    case 'fm-addr':
                        $bad_fields[$field] = 'Address';
                        break;
                    case 'fm-town':
                        $bad_fields[$field] = 'City';
                        break;
                    case 'fm-state':
                        $bad_fields[$field] = Locale::spell('State');
                        break;
                    case 'fm-postcode':
                        $bad_fields[$field] = Locale::spell('Zip Code');
                        break;
                    case 'where-own':
                        $bad_fields[$field] = 'Moving Details';
                        break;
                    case 'inquire_type':
                        $bad_fields[$field] = 'Subject';
                        break;
                    default:
                        $bad_fields[$field] = ucwords($field);
                        break;
                }
            }
        }

        return array ( $not_allowed, $bad_fields );
    }

    /**
     * Check for a valid SHA1 hash
     *
     * @param string $md5_string
     * @return bool
     */
    public static function sha1($sha1_string)
    {
        $sha1_string = trim($sha1_string);
        return preg_match('/^[a-f0-9]{40}$/i', $sha1_string, $matches) ? true : false;
    }

    /**
     * Check for a valid GUID
     * 00000000-0000-0000-0000-000000000000
     *
     * @param string $guid_string
     * @return bool
     */
    public static function guid($guid_string)
    {
        $guid_string = trim($guid_string);
        return preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $guid_string, $matches) ? true : false;
    }

    /**
     * Confirms whether file is a valid JPEG, GIF, or PNG
     * @param string $file_path
     * @return boolean
     */
    public static function image($file_path, $extention = '')
    {
        $extention = $extention ?: end(explode('.', $file_path));
        return !(!in_array(strtolower($extention), ['jpeg', 'jpg', 'gif', 'png']) || !in_array(exif_imagetype($file_path), array(IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_PNG)));
    }

    /**
     * Validates a password
     * Password must be between 6 - 100 Characters
     * Require at least 1 number (0-9)
     * Require at least 1 leader (a-z) (A-Z)
     * @param string $password
     * @return bool true
     * @throws Exception_ValidationError
     */
    public static function password ($password) {
        // Password must be between 6 - 100 Characters
        $length = strlen(trim($password));
        if ($length < 6) {
            throw new Exception_ValidationError (
                __('Password cannot be less than 6 characters.')
            );
        }
        if ($length > 100) {
            throw new Exception_ValidationError (
                __('Password cannot be more than 100 characters.')
            );
        }

        // Require at least 1 number (0-9)
        if (!preg_match('/[0-9]/', $password)) {
            throw new Exception_ValidationError (
                __('Your new password must contain at least 1 number.')
            );
        }

        // Require at least 1 letter (a-z) (A-Z)
        if (!preg_match('/[a-zA-Z]/', $password)) {
            throw new Exception_ValidationError (
                __('Your new password must contain at least 1 letter.')
            );
        }
        return true;
    }
}
