<?php
namespace REW\Model\Office\Search;

/**
 * OfficeResult
 * @package REW\Model\Office\Search
 */
class OfficeResult
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
    protected $title;

    /**
     * @var string
     */
    protected $description;

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
    protected $fax;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $zip;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var bool
     */
    protected $display;

    /**
     * @var int
     */
    protected $sort;

    /**
     * @var bool
     */
    protected $featured;

    /**
     * @return int
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
    public function getPhone()
    {
        return $this->phone;
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
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getDisplay()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
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
     * @param string $description
     * @return self
     */
    public function withDescription($description)
    {
        $clone = clone $this;
        $clone->description = $description;
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
     * @param string $phone
     * @return self
     */
    public function withPhone($phone)
    {
        $clone = clone $this;
        $clone->phone = $phone;
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
     * @param string $address
     * @return self
     */
    public function withAddress($address)
    {
        $clone = clone $this;
        $clone->address = $address;
        return $clone;
    }

    /**
     * @param string $city
     * @return self
     */
    public function withCity($city)
    {
        $clone = clone $this;
        $clone->city = $city;
        return $clone;
    }

    /**
     * @param string $state
     * @return self
     */
    public function withState($state)
    {
        $clone = clone $this;
        $clone->state = $state;
        return $clone;
    }

    /**
     * @param string $zip
     * @return self
     */
    public function withZip($zip)
    {
        $clone = clone $this;
        $clone->zip = $zip;
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
     * @param bool $display
     * @return self
     */
    public function withDisplay($display)
    {
        $clone = clone $this;
        $clone->display = $display;
        return $clone;
    }

    /**
     * @param string $sort
     * @return self
     */
    public function withSort($sort)
    {
        $clone = clone $this;
        $clone->sort = $sort;
        return $clone;
    }
}
