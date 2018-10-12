<?php
namespace REW\Datastore\User;

use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\User\SessionInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\UserInterface;
use REW\Core\Interfaces\Util\CMSInterface;
use REW\Core\Interfaces\FormatInterface;
use REW\Model\User\Form\UserRequestModel;
use REW\Model\User\Form\UserResultModel;
use REW\Factory\User\Form\UserResultFactory;
use \Validate;

/**
 * Class FormDatastore
 * @package REW\Datastore\User
 */
class FormDatastore
{
    /**
     * @var string
     */
    const REGISTER_MESSAGE = '<p>A new user has registered at <a href="%s">%s</a>.</p><p>The following information was collected about the user:</p>%s';

    /**
     * @var DBFactoryInterface
     */
    protected $dbFactory;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * @var CMSInterface
     */
    protected $cms;

    /**
     * @var FormatInterface
     */
    protected $format;

    /**
     * @var UserResultFactory
     */
    protected $userResultFactory;

    /**
     * @var \REW\Core\Interfaces\UserInterface
     */
    protected $user;

    /**
     * @param DBFactoryInterface $dbFactory
     * @param SessionInterface $session
     * @param SettingsInterface $settings
     * @param CMSInterface $cms,
     * @param FormatInterface $format,
     * @param UserFactory $userFactory
     * @param UserInterface $user
     */
    public function __construct(
        DBFactoryInterface $dbFactory,
        SessionInterface $session,
        SettingsInterface $settings,
        CMSInterface $cms,
        FormatInterface $format,
        UserResultFactory $userResultFactory,
        UserInterface $user
    ) {
        $this->dbFactory = $dbFactory;
        $this->session = $session;
        $this->settings = $settings;
        $this->cms = $cms;
        $this->format = $format;
        $this->userResultFactory= $userResultFactory;
        $this->user = $user;
    }

    /**
     * @param UserRequestModel $userRequest
     * @return UserResultModel
     * @throws \PDOException on database error
     */
    public function createUser(UserRequestModel $userRequest)
    {
        // Validate User Data
        $validationErrors = $this->validateRequest($userRequest);
        if (!empty($validationErrors)) {
            return $this->userResultFactory->createFromArray([
                'errors' => $validationErrors
            ]);
        }

        // Save User Data
        $this->saveUserInfo($userRequest);

        // Lead Password
        if ($this->settings->SETTINGS['registration_password']) {
            $password = $this->user->encryptPassword($userRequest->getPassword());
        }

        // Capture Lead
        $requestListing = $userRequest->getListing();
        require_once $this->settings->DIRS['BACKEND'] . 'inc/php/functions/funcs.ContactSnippets.php';
        $userId = collectContactData(array (
            'first_name'   => $userRequest->getFirstName(),
            'last_name'    => $userRequest->getLastName(),
            'password'     => $password,
            'email'        => $userRequest->getEmail(),
            'phone_cell'   => $userRequest->getPhone(),
            'opt_marketing' => $userRequest->getOptMarketing(),
            'opt_texts'     => ($userRequest->getOptTexts()) ? 'in' : 'out',
            'contact_method'=> $userRequest->getContactMethod(),
            'listing'      => $requestListing ? [
                'ListingMLS' =>  $requestListing->getId(),
                'ListingType' =>  $requestListing->getPropertyType(),
                'ListingFeed' =>  $requestListing->getFeed(),
            ] : [],
            'forms'        => $userRequest->getForm(),
            'message'      => $this->getRegistrationMessage()
        ), $userRequest->getAutoresponder());

        // Set User Session ID
        $this->session->setUserId($userId);

        // Validate User Session
        $this->session->validate();

        // Get User Row
        $database = $this->dbFactory->get();
        $userQuery = $database->prepare(sprintf(
            "SELECT * FROM `%s` WHERE `id` = ?;",
            $this->settings['TABLES']['LM_LEADS']
        ));
        $userQuery->execute([$userId]);
        $userData = $userQuery->fetch();

        return $this->userResultFactory->createFromArray([
            'success' => 'You have successfully been registered!',
            'ppc' => $this->cms->getPPCSettings(),
            'data' => $userData
        ]);
    }

    /**
     * @param UserRequestModel $userRequest
     * @return UserResultModel
     * @throws \PDOException on database error
     */
    public function verifyUser(UserRequestModel $userRequest)
    {
        // Validate User Data
        $verificationErrors = $this->validateVerification($userRequest);
        if (!empty($verificationErrors)) {
            return $this->userResultFactory->createFromArray([
                'errors' => $verificationErrors
            ]);
        }

        // Get User Row
        $database = $this->dbFactory->get();
        $userQuery = $database->prepare(sprintf(
            "UPDATE `%s` SET `verified` = 'yes' WHERE `id` = ?;",
            $this->settings['TABLES']['LM_LEADS']
        ));
        if ($userQuery->execute([$userRequest->getId()])) {
            // Validate User
            $this->session->validate();

            // Lead is Verified
            $this->session->saveInfo('verified', 'yes');
        }

        return $this->userResultFactory->createFromArray([
            'success' => 'You have successfully been verified!'
        ]);
    }

    /**
     * Validate Data and return any errors found
     * @param UserRequestModel $userRequest
     * @return array $errors
     * @todo Move to seperate validator
     * @throws InvalidArgumentException
     */
    protected function validateRequest($userRequest)
    {
        // Errors found
        $errors = [];

        // Check For Spam
        try {
            if (!empty($userRequest->getHoneypot())) {
                throw new \InvalidArgumentException();
            }
            require_once $this->settings->DIRS['BACKEND'] . 'inc/php/routine.spam-stop.php';
            $spam = checkForSpam($package);
            if ($spam || !$package['is_browser'] || $fake) {
                throw new \InvalidArgumentException();
            }
        } catch (\InvalidArgumentException $e) {
            $errors[] = 'We\'re sorry but you were detected as spam.';
        }

        // Check Email
        if (!Validate::email($userRequest->getEmail())) {
            $errors[] = 'Please supply a valid email address.';
        }

        // Check First Name
        if (!Validate::stringRequired($userRequest->getFirstName())) {
            $errors[] = 'Please supply your first name.';
        }

        // Check Last Name
        if (!Validate::stringRequired($userRequest->getLastName())) {
            $errors[] = 'Please supply your last name.';
        }

        // Check Phone
        if ($this->settings->SETTINGS['registration_phone']) {
            if (!Validate::stringRequired($userRequest->getPhone()) || !Validate::phone($userRequest->getPhone())) {
                $errors[] = 'Please supply a valid phone number.';
            }
        }

        // Check Password
        try {
            if ($this->settings->SETTINGS['registration_password']) {
                if (!Validate::stringRequired($userRequest->getPassword())) {
                    throw new \InvalidArgumentException('Please enter your desired password.');
                }
                if ($userRequest->getPassword() != $userRequest->getPasswordConfirmation()) {
                    throw new \InvalidArgumentException('The two passwords you supplied did not match one another.');
                }
                Validate::password($userRequest->getPassword());
            }
        } catch (\InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        } catch (\Exception_ValidationError $e) {
            $errors[] = $e->getMessage();
        }

        // Require Compliance Agreement
        global $_COMPLIANCE;
        if (!empty($_COMPLIANCE['register']['agree'])) {
            $agree = $_COMPLIANCE['register']['agree'];
            if (is_array($agree) && $userRequest->getComplianceAgree()) {
                $errors[] = 'You must agree to the <a href="' . $agree['link'] . '" target="_blank">' . $agree['title'] . '</a>.';
            }
        }

        // Check Duplicate Email
        $database = $this->dbFactory->get();
        $checkEmailQuery = $database->prepare(sprintf(
            'SELECT COUNT(*) AS `total` FROM `%s` WHERE `email` = ?%s;',
            $this->settings->TABLES['LM_LEADS'],
            !empty($this->settings->SETTINGS['registration_password']) ? ' AND `password` != \'\'' : ''
        ));
        $checkEmailQuery->execute([$userRequest->getEmail()]);
        if ($checkEmailQuery->fetchColumn()!= 0) {
            $errors[] = 'Your email address has already been registered. <a href="' . $this->settings->SETTINGS['URL_IDX_LOGIN'] . '" data-modal="login">Login here</a>';
        }

        // Check For Potentially Malicious Submission Data
        $checkFields = [
            'onc5khko' => $userRequest->getFirstName(),
            'sk5tyelo' => $userRequest->getLastName()
        ];
        list($notAllowed, $badFields) = Validate::formFields($checkFields);
        foreach ($badFields as $badField) {
            $errors[] = sprintf(
                'We are sorry.  We are unable to process your submission as your %s contains at least one of the following characters: %s',
                $badField,
                implode(', ', $this->format->htmlspecialchars($notAllowed))
            );
        }

        return $errors;
    }

    /**
     * Validate verification request
     * @param UserRequestModel $userRequest
     * @return array $errors
     */
    protected function validateVerification($userRequest)
    {
        // Errors found
        $errors = [];

        // Trim Code
        $code = $this->format->trim($userRequest->getEmailValidationCode());

        // Require Valid Verification Code
        if (!Validate::guid($code) && !Validate::sha1($code)) {
            $errors[] = 'Please enter in the validation code that was emailed to you.';
        }
        if (empty($userRequest->getId())) {
            $errors[] = 'Registration is required before email verification.';
        }

        if (empty($errors)) {
            // Get Database
            $database = $this->dbFactory->get();

            // Find Lead using Code
            if (Validate::guid($code)) {
                // Get User Row
                $guidQuery = $database->prepare(sprintf(
                    "SELECT * FROM `%s` WHERE `guid` = GuidToBinary(?) LIMIT 1;",
                    $this->settings['TABLES']['LM_LEADS']
                ));
                $guidQuery->execute([$code]);
                $lead = $guidQuery->fetch();
            } else if (Validate::sha1($code)) {
                // Get User Row
                $guidQuery = $database->prepare(sprintf(
                    "SELECT * FROM `%s` WHERE SHA1(UPPER(`email`)) = ? LIMIT 1;",
                    $this->settings['TABLES']['LM_LEADS']
                ));
                $guidQuery->execute([$code]);
                $lead = $guidQuery->fetch();
            }
            if (empty($lead) || $lead['id'] != $this->session->getUserId()) {
                $errors[] = 'There was an error with the validation code you entered.';
            }
        }
        return $errors;
    }

    /**
     * Save User Session Info
     * @param UserRequestModel $userRequest
     */
    protected function saveUserInfo(UserRequestModel $userRequest)
    {
        $this->session->saveInfo('contact_method', $userRequest->getContactMethod());
        $this->session->saveInfo('first_name', $userRequest->getFirstName());
        $this->session->saveInfo('last_name', $userRequest->getLastName());
        $this->session->saveInfo('phone_cell', $userRequest->getPhone());
        $this->session->saveInfo('email', $userRequest->getEmail());
        $this->session->saveInfo('opt_marketing', ($userRequest->getOptMarketing() ? 'in' : 'out'));
        $this->session->saveInfo('opt_texts', ($userRequest->getOptTexts() ? 'in' : 'out'));
    }

    /**
     * Get Registration Message
     * @return string
     */
    protected function getRegistrationMessage()
    {
        return sprintf(
            self::REGISTER_MESSAGE,
            $this->settings->SETTINGS['URL_IDX_REGISTER'],
            $this->settings->SETTINGS['URL_IDX_REGISTER'],
            $this->session->formatUserInfo()
        );
    }
}
