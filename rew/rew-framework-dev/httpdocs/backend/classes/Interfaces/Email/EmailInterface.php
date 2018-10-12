<?php

namespace REW\Backend\Interfaces\Email;

use REW\Backend\CMS\Interfaces\SubdomainInterface;
use REW\Backend\Exceptions\MissingId\MissingAgentException;
use REW\Backend\Exceptions\MissingId\MissingTeamException;
use \DB;
use \Auth;

interface EmailInterface
{

    /**
     * Lead Recipient Type
     * var array
     */
    const TYPE_LEADS = 'leads';

    /**
     * Lead Agents Type
     * var array
     */
    const TYPE_AGENTS = 'agents';

    /**
     * Lead Associates Type
     * var array
     */
    const TYPE_ASSOCIATES = 'associates';

    /**
     * Lead Lenders Type
     * var array
     */
    const TYPE_LENDERS = 'lenders';

    /**
     * Array of valid recipient types
     * var array
     */
    const TYPES = [
        self::TYPE_LEADS,
        self::TYPE_AGENTS,
        self::TYPE_ASSOCIATES,
        self::TYPE_LENDERS,
    ];

    /**
     * Send an email
     *
     * @param array  $recipients Recipient information
     * @param string $recipientsType
     *
     * @return string Success Description
     *
     * @throws \InvalidArgumentException
     */
    public function send(array $recipients, $recipientsType = self::TYPE_LEADS, &$errors = array());
}
