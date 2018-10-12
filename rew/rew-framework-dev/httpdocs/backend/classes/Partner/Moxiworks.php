<?php

namespace REW\Backend\Partner;

use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\Http\HostInterface;
use Backend_Lead;
use InvalidArgumentException;
use UnexpectedValueException;
use Util_Curl;

/**
 * Class Moxiworks
 * @package REW\Backend\Partner
 */
class Moxiworks
{
    /**
     * API Endpoint
     * @var string
     */
    const URL_API_ENDPOINT = 'https://api.moxiworks.com/api/';

    /**
     * API ID
     * @var string
     */
    const API_ID = '529508e8-23e7-11e8-a572-0050569c119a';

    /**
     * API Secret Key
     * @var string
     */
    const API_SECRET = 'saS4I6RUeIopi2ilifQZ3wtt';

    /**
     * API Version
     * @var string
     */
    const API_VERSION = '1';

    /**
     * @var HostInterface
     */
    protected $http_host;

    /**
     * @var SettingsInterface
     */
    protected $settings;

    /**
     * Moxiworks constructor.
     * @param HostInterface $http_host
     * @param SettingsInterface $settings
     */
    public function __construct(
        HostInterface $http_host,
        SettingsInterface $settings
    ) {
        $this->http_host = $http_host;
        $this->settings = $settings;
    }

    /**
     * @param Backend_Lead $lead
     * @throws UnexpectedValueException on API error response
     * @return mixed (bool|array)
     */
    public function pushContact(Backend_Lead $lead)
    {
        if (!$lead->getId()) {
            throw new InvalidArgumentException(
                'Lead does not have a valid ID.'
            );
        }
        $endpoint = '/contacts';
        $response = $this->executeCurlRequest(
            $endpoint,
            $this->getPushRequestParameters($lead),
            Util_Curl::REQUEST_TYPE_POST
        );
        return $this->decodeJsonResponse($response);
    }

    /**
     * @param string $email_address
     * @return mixed (bool|array)
     */
    public function getContactByEmail($email_address)
    {
        if (empty($email_address)) {
            throw new InvalidArgumentException(
                'No email address specified.'
            );
        }
        $endpoint = '/contacts';
        $response = $this->executeCurlRequest(
            $endpoint,
            $this->getIndexRequestParameters($email_address),
            Util_Curl::REQUEST_TYPE_GET
        );
        return $this->decodeJsonResponse($response);
    }

    /**
     * @param $response
     * @return mixed
     */
    protected function decodeJsonResponse($response)
    {
        $response = json_decode($response, true);
        if (isset($response['status']) && $response['status'] === 'error') {
            $messages = isset($response['messages']) ? $response['messages'] : [];
            $status_code = isset($response['status_code']) ? $response['status_code'] : 0;
            throw new UnexpectedValueException(sprintf(
                'Failed to create lead through Moxi API: %s [%s]',
                implode(', ', $messages),
                $status_code
            ));
        }
        return $response;
    }

    /**
     * @param int $id
     * @return string
     */
    protected function generateContactId ($id)
    {
        return sprintf(
            'REW-%s-%s',
            preg_replace('/[^a-zA-Z0-9]/', '-', $this->http_host->getHost()),
            $id
        );
    }

    /**
     * @param string $email_address
     * @return array
     */
    protected function getIndexRequestParameters ($email_address)
    {
        return [
            'agent_uuid' => $this->getAgentUUID(),
            'email_address' => $email_address
        ];
    }

    /**
     * @param Backend_Lead $lead
     * @return array
     */
    protected function getPushRequestParameters (Backend_Lead $lead)
    {
        $contact_id = $this->generateContactId($lead->info('id'));
        return [
            'agent_uuid' => $this->getAgentUUID(),
            'partner_contact_id' => $contact_id,
            'contact_name' => sprintf('%s %s', $lead->info('first_name'), $lead->info('last_name')),
            'primary_email_address' => $lead->info('email'),
            'primary_phone_number' => !empty($lead->info('phone')) ? $lead->info('phone') : $lead->info('phone_cell'),
            'home_street_address' => $lead->info('address1'),
            'home_city' => $lead->info('city'),
            'home_state' => $lead->info('state'),
            'home_zip' => $lead->info('zip')
        ];
    }

    /**
     * @param $endpoint
     * @param array $params
     * @param $request_type
     * @return mixed (bool|string)
     */
    protected function executeCurlRequest($endpoint, $params = [], $request_type = Util_Curl::REQUEST_TYPE_GET)
    {
        // Set Moxi API Base URL
        $endpoint_url = self::URL_API_ENDPOINT . $endpoint;

        $headers = [
            sprintf(
                'Authorization: Basic %s',
                base64_encode(sprintf(
                    '%s:%s',
                    self::API_ID,
                    self::API_SECRET
                ))
            ),
            sprintf(
                'Accept: application/vnd.moxi-platform+json;version=%s',
                self::API_VERSION
            ),
            'Content-Type: application/x-www-form-urlencoded',
            'Cookie: _wms_svc_public_session'
        ];
        return Util_Curl::executeRequest($endpoint_url, $params, $request_type, [
            CURLOPT_HTTPHEADER => $headers
        ]);
    }

    /**
     * Get the UUID used for API request validation
     *
     * @return string
     */
    protected function getAgentUUID()
    {
        return (isset($this->settings->MODULES['REW_PARTNERS_MOXI_CRM']) ? $this->settings->MODULES['REW_PARTNERS_MOXI_CRM'] : null);
    }
}