<?php
namespace REW\Model\SocialConnect;

/**
 * SocialConnectModel
 * @package REW\Model\SocialConnect
 */
class SocialConnectModel
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $connectUrl;

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getConnectUrl()
    {
        return $this->connectUrl;
    }

    /**
     * @param string $id
     * @return self
     */
    public function withId($id)
    {
        $clone = clone $this;
        $clone->id = $id;
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
     * @param string $connectUrl
     * @return self
     */
    public function withConnectUrl($connectUrl)
    {
        $clone = clone $this;
        $clone->connectUrl = $connectUrl;
        return $clone;
    }
}
