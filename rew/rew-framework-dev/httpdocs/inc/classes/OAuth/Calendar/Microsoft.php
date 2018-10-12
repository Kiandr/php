<?php

/**
 * OAuth_Calendar_microsoft is used for connecting to microsoft Calendar via OAuth
 *
 * @link https://manage.dev.live.com/
 */
class OAuth_Calendar_Microsoft extends OAuth_Calendar
{

    /**
     * Provider Name
     * @var string
     */
    protected $name = 'Windows Live';

    /**
     * Base URL
     * @var string
     */
    protected $base_url = 'https://apis.live.net/v5.0/';

    /**
     * Base HTTP Request URL
     * @var string
     */
    protected $url_calendar = 'https://apis.live.net/v5.0/me/calendars';

    /**
     * Event URL
     * @var string
     */
    protected $url_event = 'https://apis.live.net/v5.0/me/events';

    /**
     * Get Calendar
     * @param calendar_id string
     * @return boolean
     */
    protected function getCalendar()
    {

        // OAuth Request
        $request = new OAuth_Request('GET', $this->url_calendar, array(
            'access_token'  => $this->access_token,
            'scope'         => $this->url_scope,
        ));

        // Execute Request
        $response = $request->execute();

        // Error Occurred
        if ($request->getCode() != 200) {
            throw new Exception_OAuthCalendarError;
        }

        // Require Response
        if (empty($response)) {
            return false;
        }

        // Parse Response
        $parts = json_decode($response, true);

        // Require Calendar ID
        if (empty($parts['data'][0]['id'])) {
            return false;
        }

        // Set Calendar ID
        $this->calendar_id = $parts['data'][0]['id'];

        return true;
    }


    /**
     * Get Calendar ID
     * @param object $event OAuth_Event
     * @param string $type push type ("INSERT", "UPDATE")
     * @return boolean
     */
    public function push(OAuth_Event $event, $type)
    {

        // Initialze data array
        $data = array();

        // Get Calendar ID
        if ($this->getCalendar()) {
            // Build Data Array
            $data['start_time'] =  date('c', strtotime($event->start));
            $data['end_time'] =  date('c', strtotime($event->end));
            $data['description'] = "No Description";

            if (!empty($event->title)) {
                $data['name'] = $event->title . (!empty($event->type) ? ' (' . $event->type . ')' : ' (Un-Categorized)');
            }
            if (!empty($event->description)) {
                $data['description'] = $event->description;
            }
            if (!empty($event->all_day_event)) {
                $data['all_day_event'] = $event->all_day_event;
            }

            switch ($type) {
                case 'INSERT':
                    // Build Request URL
                    $request_url =  $this->url_event . '?access_token=' . urlencode($this->access_token);
                    // OAuth Request
                    $request = new OAuth_Request('POST', $request_url, $data, array(), true);
                    break;
                case 'UPDATE':
                    // Build Request URL
                    $request_url = $this->base_url . $event->event_id . '?access_token=' . urlencode($this->access_token);
                    // OAuth Request
                    $request = new OAuth_Request('PUT', $request_url, $data, array(), true);
                    break;
            }

            // Execute Request
            $response = $request->execute();

            // Error Occurred
            if (!in_array($request->getCode(), array(201, 200))) {
                throw new Exception_OAuthCalendarError;
            }

            // Require Response
            if (empty($response)) {
                return false;
            }

            // Parse Response
            $parts = json_decode($response, true);

            if (!empty($parts['id'])) {
                return $parts['id'];
            } else {
                return false;
            }
        }
    }

    /**
     * Delete a calendar event
     * @param string $id Event ID
     * @return boolean
     */
    public function deleteEvent($id)
    {

        // OAuth Request
        $request = new OAuth_Request('DELETE', $this->base_url . '/' . $id . '?access_token=' . urlencode($this->access_token), array(), array(), true);

        // Execute Request
        $response = $request->execute();

        // Error Occurred
        if ($request->getCode() != 204) {
            throw new Exception_OAuthCalendarError;
        }

        // Empty Response is successful
        return empty($response);
    }

    /**
     * Validates access token
     * @return boolean
     */
    public function validateToken()
    {

        // Outlook Calendar Push Disabled
        if ($this->authuser->info('microsoft_calendar_sync') !== 'true') {
            return false;
        }

        // Get authusers data
        $network_microsoft = $this->authuser->info('network_microsoft');

        // Set Valid Token Flag
        $valid_token = false;

        // Get Calendar providers
        $providers = OAuth_Login::getCalendarProviders();

        // Ensure we have the network set
        if (!empty($network_microsoft)) {
            // Decode network data
            $network_microsoft = get_object_vars(json_decode($network_microsoft));

            if (!empty($providers['Microsoft'])) {
                if (!empty($network_microsoft['access_token'])) {
                    try {
                        $valid_token = true;
                        if (!empty($network_microsoft['refresh_token']) && !empty($network_microsoft['expire_time']) && $this->isExpired($network_microsoft['expire_time'])) {
                            $microsoft_oauth = new OAuth_Login_Microsoft(Settings::getInstance()->SETTINGS['microsoft_apikey'], Settings::getInstance()->SETTINGS['microsoft_secret']);
                            $token = $microsoft_oauth->refreshToken($network_microsoft['refresh_token'], true);
                            if (!empty($token)) {
                                $network_microsoft['access_token'] = $token['access_token'];
                                $this->access_token = $token['access_token'];
                                // Calculate expiry time
                                if (!empty($response['expires_in'])) {
                                    $response['expire_time'] = time() + $response['expires_in'];
                                }
                                $network_microsoft = json_encode($network_microsoft);

                                // App DB
                                $db = DB::get();

                                if ($db->fetch("UPDATE `" . LM_TABLE_AGENTS . "` SET `network_microsoft` = :network_microsoft WHERE `id` = :id", [
                                    'network_microsoft' => $network_microsoft,
                                    'id' => $this->authuser->info('id')
                                ])) {
                                    $this->authuser->info('network_microsoft', $network_microsoft);
                                }
                            } else {
                                $valid_token = false;
                            }
                        } else {
                            $this->access_token = $network_microsoft['access_token'];
                        }
                    } catch (Exception_OAuthCalendarError $e) {
                        Log::error($e);
                    } catch (Exception_OAuthLoginError $e) {
                        Log::error($e);
                    }
                }
            }
        }

        // Open a popup if we don't have a valid token
        if (!$valid_token) {
            $providerUrl = $providers['Microsoft']['connect'];
            if ($providerUrl) {
                $this->page->writeJS("var popup = (window.open(" . json_encode($providerUrl) . ", 'rewmcalendar', 'height=600,width=850,scrollbars=1,location=no,toolbar=no,resizable=yes')); if (popup) popup.focus();");
            }
            return false;
        } else {
            return true;
        }
    }
}
