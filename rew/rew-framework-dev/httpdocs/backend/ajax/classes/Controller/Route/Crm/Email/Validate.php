<?php

namespace REW\Api\Internal\Controller\Route\Crm\Email;

use REW\Api\Internal\Exception\BadRequestException;
use REW\Api\Internal\Interfaces\ControllerInterface;
use Slim\Http\Response;
use Slim\Http\Request;
use \Validate as EmailValidate;

/**
 * Email Address Validate Controller
 * @package REW\Api\Internal\Controller
 */
class Validate implements ControllerInterface
{
    /**
     * @var array
     */
    const STATUS_CODES = [
        1 => 'EVTASK_SYNTAX_CHECK',
        2 => 'EVTASK_MX_RECORDS',
        3 => 'EVTASK_MX_PRE_CONNECT',
        4 => 'EVTASK_MX_CONNECT',
        5 => 'EVTASK_MX_TRANSMISSION',
        6 => 'EVTASK_VALIDATION',
        101 => 'EVSTATUS_SYNTAX_OK',
        102 => 'EVSTATUS_MX_RECORDS_OK',
        103 => 'EVSTATUS_MX_CONNECT_OK',
        104 => 'EVSTATUS_MX_TRANSMISSION_OK',
        105 => 'EVSTATUS_EMAIL_ACCEPTED',
        901 => 'EVSTATUS_SYNTAX_ERROR',
        902 => 'EVSTATUS_MX_RECORDS_ERROR',
        903 => 'EVSTATUS_MX_CONNECT_ERROR',
        904 => 'EVSTATUS_MX_TRANSMISSION_ERROR',
        905 => 'EVSTATUS_EMAIL_REJECTED',
    ];

    /**
     * @var array
     */
    protected $get;

    /**
     * Does nothing!
     */
    public function __construct() {}

    /**
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     */
    public function __invoke(Request $request, Response $response, $routeParams = [])
    {
        $this->get = $request->get();

        $body = $this->verifyEmails();
        $response->setBody(json_encode($body));
    }

    /**
     * @throws NotFoundException
     * @return array
     */
    protected function verifyEmails()
    {
        if (empty($this->get['addresses'])) {
            throw new BadRequestException('No email addresses were provided.');
        }

        $addresses = is_array($this->get['addresses'])
            ? $this->get['addresses']
            : [$this->get['addresses']];

        $response = ['emails' => []];

        foreach ($addresses as $address) {
            $valid = EmailValidate::email($address, true, $code);
            $status = (!$valid
                ? ((!empty(self::STATUS_CODES[$code])) ? self::STATUS_CODES[$code] : 'n/a')
                : null);

            $response['emails'][] = [
                'address' => $address,
                'validated' => $valid,
                'reason_not_valid' => $status
            ];
        }

        return $response;
    }
}