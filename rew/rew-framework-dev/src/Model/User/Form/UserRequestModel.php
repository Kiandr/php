<?php
namespace REW\Model\User\Form;

use REW\Model\IDX\Search\ListingResult;
use \InvalidArgumentException;

/**
 * Class UserRequestModel
 * @package REW\Model\User\
 */
class UserRequestModel
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $passwordConfirmation;

    /**
     * @var string
     */
    protected $form;

    /**
     * @var int
     */
    protected $autoresponder;

    /**
     * @var bool
     */
    protected $optMarketing;

    /**
     * @var bool
     */
    protected $optTexts;

    /**
     * @var string
     */
    protected $contactMethod;

    /**
     * @var bool
     */
    protected $agree;

    /**
     * @var ListingResult
     */
    protected $listing;

    /**
     * @var string
     */
    protected $honeypot;

    /**
     * @var string
     */
    protected $emailValidationCode;

    /**
     * @param string $id
     * @return UserRequestModel
     */
    public function withId($id)
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $firstName
     * @return UserRequestModel
     */
    public function withFirstName($firstName)
    {
        $clone = clone $this;
        $clone->firstName = $firstName;
        return $clone;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $lastName
     * @return UserRequestModel
     */
    public function withLastName($lastName)
    {
        $clone = clone $this;
        $clone->lastName = $lastName;
        return $clone;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $email
     * @return UserRequestModel
     */
    public function withEmail($email)
    {
        $clone = clone $this;
        $clone->email = $email;
        return $clone;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $phone
     * @return UserRequestModel
     */
    public function withPhone($phone)
    {
        $clone = clone $this;
        $clone->phone = $phone;
        return $clone;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $password
     * @return UserRequestModel
     */
    public function withPassword($password)
    {
        $clone = clone $this;
        $clone->password = $password;
        return $clone;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $passwordConfirmation
     * @return UserRequestModel
     */
    public function withPasswordConfirmation($passwordConfirmation)
    {
        $clone = clone $this;
        $clone->passwordConfirmation= $passwordConfirmation;
        return $clone;
    }

    /**
     * @return string
     */
    public function getPasswordConfirmation()
    {
        return $this->passwordConfirmation;
    }

    /**
     * @param string $form
     * @return UserRequestModel
     */
    public function withForm($form)
    {
        $clone = clone $this;
        $clone->form = $form;
        return $clone;
    }

    /**
     * @return string
     */
    public function getForm()
    {
        return $this->form;
    }


    /**
     * @param int $autoresponder
     * @return UserRequestModel
     */
    public function withAutoresponder($autoresponder)
    {
        $clone = clone $this;
        $clone->autoresponder = $autoresponder;
        return $clone;
    }

    /**
     * @return int
     */
    public function getAutoresponder()
    {
        return $this->autoresponder;
    }

    /**
     * @param bool $optMarketing
     * @return UserRequestModel
     */
    public function withOptMarketing($optMarketing)
    {
        if (!is_bool($optMarketing)) {
            throw new InvalidArgumentException('$optMarketing must be a boolean!');
        }
        $clone = clone $this;
        $clone->optMarketing = $optMarketing;
        return $clone;
    }

    /**
     * @return string
     */
    public function getOptMarketing()
    {
        return $this->optMarketing;
    }

    /**
     * @param bool $optTexts
     * @return UserRequestModel
     */
    public function withOptTexts($optTexts)
    {
        if (!is_bool($optTexts)) {
            throw new InvalidArgumentException('$optTexts must be a boolean!');
        }
        $clone = clone $this;
        $clone->optTexts = $optTexts;
        return $clone;
    }

    /**
     * @return string
     */
    public function getOptTexts()
    {
        return $this->optTexts;
    }

    /**
     * @param string $contactMethod
     * @return UserRequestModel
     */
    public function withContactMethod($contactMethod)
    {
        $clone = clone $this;
        $clone->contactMethod = $contactMethod;
        return $clone;
    }

    /**
     * @return string
     */
    public function getContactMethod()
    {
        return $this->contactMethod;
    }

    /**
     * @param bool $lastName
     * @return UserRequestModel
     */
    public function withComplianceAgree($agree)
    {
        $clone = clone $this;
        $clone->agree = $agree;
        return $clone;
    }

    /**
     * @return bool
     */
    public function getComplianceAgree()
    {
        return $this->agree;
    }

    /**
     * @param ListingResult $listing
     * @return UserRequestModel
     */
    public function withListing(ListingResult $listing = null)
    {
        if (isset($listing) && !($listing instanceof $listing)) {
            throw new InvalidArgumentException('$listing must be a string!');
        }
        $clone = clone $this;
        $clone->listing = $listing;
        return $clone;
    }

    /**
     * @return ListingResult
     */
    public function getListing()
    {
        return $this->listing;
    }

    /**
     * @param string $honeypot
     * @return UserRequestModel
     */
    public function withHoneypot($honeypot)
    {
        $clone = clone $this;
        $clone->honeypot= $honeypot;
        return $clone;
    }

    /**
     * @return string
     */
    public function getHoneypot()
    {
        return $this->honeypot;
    }

    /**
     * @param string $emailValidationCode
     * @return UserRequestModel
     */
    public function withEmailValidationCode($emailValidationCode)
    {
        $clone = clone $this;
        $clone->emailValidationCode = $emailValidationCode;
        return $clone;
    }

    /**
     * @return string
     */
    public function getEmailValidationCode()
    {
        return $this->emailValidationCode;
    }
}
