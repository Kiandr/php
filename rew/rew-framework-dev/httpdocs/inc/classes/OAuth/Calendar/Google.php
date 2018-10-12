<?php

/**
 * OAuth_Calendar_Google is used for connecting to Google Calendar via OAuth
 *
 * @link https://code.google.com/apis/console/#access
 */
class OAuth_Calendar_Google extends OAuth_Calendar
{

    /**
     * Provider Name
     * @var string
     */
    protected $name = 'Google';

    /**
     * Base HTTP Request URL
     * @var string
     */
    protected $url_calendar = 'https://www.googleapis.com/calendar/v3/calendars/';

    /**
     * Get Calendar
     * @param calendar_id string
     * @return boolean
     */
    protected function getCalendar()
    {

        // OAuth Request
        $request = new OAuth_Request('GET', $this->url_calendar . 'primary', array(
            'access_token'  => $this->access_token,
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

        // Require Access Token
        if (empty($parts['id'])) {
            return false;
        }

        // Set Access Token
        $this->calendar_id = $parts['id'];

        // Set Timezone
        $this->timezone = $parts['timeZone'];

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
            // Build data array
            if ($event->all_day_event) {
                $data['start'] = array(
                    'date' => date('Y-m-d', strtotime($event->start)),
                );

                $data['end'] = array(
                    'date' => date('Y-m-d', strtotime($event->end)),
                );
            } else {
                // Timezone placeholder
                $timezone_placeholder = date_default_timezone_get();

                // Set timezone to match Google Calendar
                date_default_timezone_set($this->getTimezone());

                $data['start'] = array(
                    'dateTime' => date('c', strtotime($event->start)),
                );

                $data['end'] =array(
                    'dateTime' => date('c', strtotime($event->end)),
                );

                // Reset Timezone
                date_default_timezone_set($timezone_placeholder);
            }

            if (!empty($event->title)) {
                $data['summary'] = $event->title . (!empty($event->type) ? ' (' . $event->type . ')' : ' (Un-Categorized)');
            }
            if (!empty($event->description)) {
                $data['description'] = $event->description;
            }
            if (!empty($event->colorId)) {
                $data['colorId'] = $event->colorId != 'NULL' ? $event->colorId : '0';
            }

            switch ($type) {
                case 'INSERT':
                    // Build Request URL
                    $request_url = $this->url_calendar . urlencode($this->calendar_id) . '/events?access_token=' . urlencode($this->access_token);
                    // OAuth Request
                    $request = new OAuth_Request('POST', $request_url, $data, array(), true);
                    break;
                case 'UPDATE':
                    // Build Request URL
                    $request_url = $this->url_calendar . urlencode($this->calendar_id) . '/events/' . $event->event_id. '?access_token=' . urlencode($this->access_token);
                    // OAuth Request
                    $request = new OAuth_Request('PUT', $request_url, $data, array(), true);
                    break;
            }

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

            if (!empty($parts['id'])) {
                return $parts['id'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Delete a calendar event
     * @param string $id Event ID
     * @return boolean
     */
    public function deleteEvent($id)
    {

        // Get Calendar ID
        if ($this->getCalendar()) {
            // Build Request URL
            $request_url = $this->url_calendar . urlencode($this->calendar_id) . '/events/' . $id. '?access_token=' . urlencode($this->access_token);

            // OAuth Request
            $request = new OAuth_Request('DELETE', $request_url, array(), array(), true);

            // Execute Request
            $response = $request->execute();

            // Error Occurred
            if ($request->getCode() != 204) {
                throw new Exception_OAuthCalendarError;
            }

            // Empty Response is successful
            return empty($response);
        } else {
            return false;
        }
    }

    /**
     * Validates access token
     * @return boolean
     */
    public function validateToken()
    {

        // Google Calendar Push Disabled
        if ($this->authuser->info('google_calendar_sync') !== 'true') {
            return false;
        }

        // Get authusers data
        $network_google = $this->authuser->info('network_google');

        // Set Valid Token Flag
        $valid_token = false;

        // Get Calendar providers
        $providers = OAuth_Login::getCalendarProviders();

        // Ensure we have the network set
        if (!empty($network_google)) {
            // Decode network data
            $network_google = get_object_vars(json_decode($network_google));

            if (!empty($providers['Google'])) {
                if (!empty($network_google['access_token'])) {
                    try {
                        $valid_token = true;
                        if (!empty($network_google['refresh_token']) && !empty($network_google['expire_time']) && $this->isExpired($network_google['expire_time'])) {
                            $google_oauth = new OAuth_Login_Google(Settings::getInstance()->SETTINGS['google_apikey'], Settings::getInstance()->SETTINGS['google_secret']);
                            $token = $google_oauth->refreshToken($network_google['refresh_token'], true);
                            if (!empty($token)) {
                                $network_google['access_token'] = $token['access_token'];
                                $this->access_token = $token['access_token'];

                                // Calculate expiry time
                                if (!empty($response['expires_in'])) {
                                    $response['expire_time'] = time() + $response['expires_in'];
                                }

                                $network_google = json_encode($network_google);

                                // App DB
                                $db = DB::get();

                                if ($db->fetch("UPDATE `" . LM_TABLE_AGENTS . "` SET `network_google` = :network_google WHERE `id` = :id", [
                                    'network_google' => $network_google,
                                    'id' => $this->authuser->info('id')
                                ])) {
                                    $this->authuser->info('network_google', $network_google);
                                }
                            } else {
                                $valid_token = false;
                            }
                        } else {
                            $this->access_token = $network_google['access_token'];
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
            $providerUrl = $providers['Google']['connect'];
            if ($providerUrl) {
                $this->page->writeJS("var popup = (window.open(" . json_encode($providerUrl) . ", 'rewgcalendar', 'height=600,width=850,scrollbars=1,location=no,toolbar=no,resizable=yes')); if (popup) popup.focus();");
            }
            return false;
        } else {
            return true;
        }
    }

    /*
	 * Gets the timezone associated with the current calendar
	 * @return string
	 */
    public function getTimezone()
    {
        return $this->timezone;
    }
}
