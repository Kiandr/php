<?php
namespace REW\Model\Agent\Search;

/**
 * AgentResult
 * @package REW\Model\Agent\Search
 */
class AgentResult
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $link;

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
    protected $title;

    /**
     * @var int
     */
    protected $officeId;

    /**
     * @var string
     */
    protected $officePhone;

    /**
     * @var string
     */
    protected $homePhone;

    /**
     * @var string
     */
    protected $cellPhone;

    /**
     * @var boolean
     */
    protected $display;

    /**
     * @var boolean
     */
    protected $display_feature;

    /**
     * @var string
     */
    protected $remarks;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var string
     */
    protected $agent_id;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return int
     */
    public function getOfficeId()
    {
        return $this->officeId;
    }

    /**
     * @return string
     */
    public function getOfficePhone()
    {
        return $this->officePhone;
    }

    /**
     * @return string
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * @return string
     */
    public function getCellPhone()
    {
        return $this->cellPhone;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @return boolean
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * @return boolean
     */
    public function getDisplayFeature()
    {
        return $this->display_feature;
    }

    /**
     * Get Agent Feed Id
     */
    public function getAgentId()
    {
        return $this->agent_id;
    }
    /**
     * @param int $id
     * @return self
     */
    public function withId($id)
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    /**
     * @param string $link
     * @return self
     */
    public function withLink($link)
    {
        $clone = clone $this;
        $clone->link = $link;
        return $clone;
    }

    /**
     * @param string $firstName
     * @return self
     */
    public function withFirstName($firstName)
    {
        $clone = clone $this;
        $clone->firstName = $firstName;
        return $clone;
    }

    /**
     * @param string $lastName
     * @return self
     */
    public function withLastName($lastName)
    {
        $clone = clone $this;
        $clone->lastName = $lastName;
        return $clone;
    }

    /**
     * @param string $email
     * @return self
     */
    public function withEmail($email)
    {
        $clone = clone $this;
        $clone->email = $email;
        return $clone;
    }

    /**
     * @param string $title
     * @return self
     */
    public function withTitle($title)
    {
        $clone = clone $this;
        $clone->title = $title;
        return $clone;
    }

    /**
     * @param int $officeId
     * @return self
     */
    public function withOfficeId($officeId)
    {
        $clone = clone $this;
        $clone->officeId = $officeId;
        return $clone;
    }

    /**
     * @param string $officePhone
     * @return self
     */
    public function withOfficePhone($officePhone)
    {
        $clone = clone $this;
        $clone->officePhone = $officePhone;
        return $clone;
    }

    /**
     * @param string $homePhone
     * @return self
     */
    public function withHomePhone($homePhone)
    {
        $clone = clone $this;
        $clone->homePhone = $homePhone;
        return $clone;
    }

    /**
     * @param string $cellPhone
     * @return self
     */
    public function withCellPhone($cellPhone)
    {
        $clone = clone $this;
        $clone->cellPhone = $cellPhone;
        return $clone;
    }

    /**
     * @param string $fax
     * @return self
     */
    public function withFax($fax)
    {
        $clone = clone $this;
        $clone->fax = $fax;
        return $clone;
    }

    /**
     * @param string $remarks
     * @return self
     */
    public function withRemarks($remarks)
    {
        $clone = clone $this;
        $clone->remarks = $remarks;
        return $clone;
    }

    /**
     * @param boolean $display
     * @return self
     */
    public function withDisplay($display)
    {
        $clone = clone $this;
        $clone->display = $display;
        return $clone;
    }

    /**
     * @param boolean $display_feature
     * @return self
     */
    public function withDisplayFeature($display_feature)
    {
        $clone = clone $this;
        $clone->display_feature = $display_feature;
        return $clone;
    }

    /**
     * @param string $image
     * @return self
     */
    public function withImage($image)
    {
        $clone = clone $this;
        $clone->image = $image;
        return $clone;
    }

    /**
     * @param string $agentId
     * @return self
     */
    public function withAgentId($agentId)
    {
        $clone = clone $this;
        $clone->agent_id = $agentId;
        return $clone;
    }
}
