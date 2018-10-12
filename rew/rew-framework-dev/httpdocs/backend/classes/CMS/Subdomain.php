<?php

namespace REW\Backend\CMS;

use Psr\Http\Message\ServerRequestInterface;
use REW\Backend\Auth\Interfaces\SubdomainAuthInterface;
use REW\Backend\CMS\Interfaces\SubdomainFactory\SubdomainSettingsInterface;
use REW\Backend\CMS\Interfaces\SubdomainFactoryInterface;
use REW\Core\Interfaces\DBInterface;
use REW\Backend\CMS\Interfaces\SubdomainInterface;

/**
 * Class Subdomain
 *
 * @category Authorization
 * @package  REW\Backend\Auth
 * @author   REW Matthew Brown <brown.matthew@realestatewebmasters.com>
 */
class Subdomain implements SubdomainInterface
{

    /**
     * @var DBInterface
     */
    protected $db;

    /**
     * @var string
     */
    protected $subdomainLink;

    /**
     * @var string
     */
    protected $subdomainTitle;

    /**
     * @var string
     */
    protected $subdomainHref;

    /**
     * @var string
     */
    protected $subdomainAndHref;

    /**
     * @var string
     */
    protected $subdomainInput;

    /**
     * @var string
     */
    protected $subdomainOwnerSql;

    /**
     * @var string
     */
    protected $subdomainAssignSql;

    /**
     * @var string
     */
    protected $subdomainOgType;

    /**
     * @var string
     */
    protected $subdomainOgQuery;

    /**
     * @var SubdomainFactoryInterface
     */
    private $subdomainFactory;

    /**
     * @var ServerRequestInterface
     */
    private $serverRequest;

    /**
     * @var SubdomainSettingsInterface
     */
    private $subdomainSettings;

    /**
     * Create Subdomain Link
     *
     * @param DBInterface   $db    PDO
     * @param SubdomainFactoryInterface $subdomainFactory
     * @param ServerRequestInterface $serverRequest
     * @param SubdomainSettingsInterface $subdomainSettings
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        DBInterface $db,
        SubdomainFactoryInterface $subdomainFactory,
        ServerRequestInterface $serverRequest,
        SubdomainSettingsInterface $subdomainSettings
    ) {
        // Set Variables
        $this->db = $db;
        $this->subdomainSettings = $subdomainSettings;
        $this->subdomainFactory = $subdomainFactory;
        $this->serverRequest = $serverRequest;
    }

    /**
     * Validates that this subdomain type is valid.
     */
    public function validate()
    {
        // Ensure Valid Type
        if (!in_array($this->getType(), $this->subdomainFactory->getTypes())) {
            throw new \InvalidArgumentException(
                sprintf('An invalid type,%s, was passed to the Subdomain constructor.'),
                $this->getType()
            );
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->subdomainSettings->getId();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->subdomainSettings->getType();
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->subdomainFactory->getTypes();
    }

    /**
     * Get Upload Type for Open Graph Images
     *
     * @return string
     */
    public function getOgType()
    {
        if (!isset($this->subdomainOgType)) {
            $type = $this->getType();
            if ($type == self::DOMAIN_KEY) {
                $this->subdomainOgType = "default:og:image";
            } else {
                $this->subdomainOgType = $type . ":og:image";
            }
        }
        return $this->subdomainOgType;
    }

    /**
     * Get Upload Type for Open Graph Images
     *
     * @return string
     */
    public function getOgQuery()
    {
        if (!isset($this->subdomainOgQuery)) {
            $type = $this->getType();
            if ($type == self::DOMAIN_KEY) {
                $this->subdomainOgQuery = " WHERE `type` = '" . $this->getOgType() . "'";
            } else {
                $this->subdomainOgQuery = " WHERE `type` = '" . $this->getOgType() . "' AND `row` = '" . $this->getId() . "'";
            }
        }
        return $this->subdomainOgQuery;
    }

    /**
     * Get SQL used to find owner
     *
     * @param bool $includeShared Should shared entities be returned?
     * @return string
     */
    public function getOwnerSql($includeShared = false)
    {
        $type = $this->getType();
        if ($type == self::DOMAIN_KEY) {
            $this->subdomainOwnerSql = "`agent` = '1'";
        } else {
            $this->subdomainOwnerSql = "`" . $type . "` = " . $this->db->quote($this->getId());
        }

        if ($includeShared) {
            $this->subdomainOwnerSql = '(' . $this->subdomainOwnerSql;
            $this->subdomainOwnerSql .= " OR (";
            foreach ($this->subdomainFactory->getTypes() as $subdomainType) {
                if ($subdomainType != self::DOMAIN_KEY) {
                    $this->subdomainOwnerSql .= "`" . $subdomainType . "` IS NULL AND ";
                }
            }
            $this->subdomainOwnerSql = rtrim($this->subdomainOwnerSql, "AND ") . "))";
        }

        return $this->subdomainOwnerSql;
    }

    /**
     * Get SQL used to assign to owner
     *
     * @return string
     */
    public function getAssignSql()
    {
        if (!isset($this->subdomainAssignSql)) {
            $getType = $this->getType();
            if ($getType == self::DOMAIN_KEY) {
                $this->subdomainAssignSql = "`agent` = 1, ";
                foreach ($this->getTypes() as $type) {
                    if (!in_array($type, [self::DOMAIN_KEY, self::AGENT_SUBDOMAIN_KEY])) {
                        $this->subdomainAssignSql .= "`" . $type . "` = NULL, ";
                    }
                }
            } else {
                $assignSql = '';
                foreach ($this->getTypes() as $type) {
                    if ($type == self::DOMAIN_KEY) {
                        continue;
                    }
                    if ($getType == $type) {
                        $assignSql .= "`".$type."` = " . $this->db->quote($this->getId()) . ", ";
                    } else {
                        $assignSql .= "`".$type."` = NULL, ";
                    }
                }
                $this->subdomainAssignSql = $assignSql;
            }
        }
        return $this->subdomainAssignSql;
    }

    /**
     * Get SQL used to assign to owner
     *
     * @param bool $andHref Should the link use & instead of ? when adding to link
     *
     * @return string
     */
    public function getPostLink($andHref = false)
    {
        $type = $this->getType();
        if ($type != self::DOMAIN_KEY) {
            return ($andHref ? '&' : '?') . $type . '=' . $this->getId();
        }
        return '';
    }

    /**
     * Get SQL used to assign to owner
     *
     * @return string
     */
    public function getInput()
    {
        $type = $this->getType();
        if ($type != self::DOMAIN_KEY) {
            return '<input type="hidden" name="' . $type . '" value="' . htmlspecialchars($this->getId()) . '">';
        }
        return '';
    }

    /**
     * Get this subdomains title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->subdomainSettings->getTitle();
    }

    /**
     * Get a link to the subdomain frontend
     *
     * @return string | null
     */
    public function getLink()
    {
        return $this->subdomainSettings->getLink();
    }

    /**
     * Is subdomain www or not
     *
     * @return string
     */
    public function isPrimary()
    {
        return ($this->getType() == self::DOMAIN_KEY);
    }

    /**
     * Is the currently edited subdomain
     *
     * @param SubdomainInterface $subdomain  Subdomain to compare against
     *
     * @return bool
     */
    public function compare(SubdomainInterface $subdomain)
    {
        return $this->getId() == $subdomain->getId()
            && $this->getType() == $subdomain->getType();
    }

    /**
     * Gets the Auth handler for this subdomain.
     *
     * @return SubdomainAuthInterface
     */
    public function getAuth()
    {
        return $this->subdomainSettings->getAuth();
    }

    /**
     * Validates that our settings are adequately configured
     * @throws \Exception if this could not be adequately configured
     */
    public function validateSettings()
    {
        $this->subdomainSettings->validate();
    }

    /**
     * Get the default feed for this subdomain
     * @return string
     */
    public function getDefaultFeed()
    {
        return $this->subdomainSettings->getDefaultFeed();
    }
}
