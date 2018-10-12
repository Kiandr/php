<?php

/*******************************************************************************
Copyright 2010 VerifyEmailAddress.org
http://www.verifyemailaddress.org/
*******************************************************************************/

define('EVTASK_SYNTAX_CHECK', 1);
define('EVTASK_MX_RECORDS', 2);
define('EVTASK_MX_PRE_CONNECT', 3);
define('EVTASK_MX_CONNECT', 4);
define('EVTASK_MX_TRANSMISSION', 5);
define('EVTASK_VALIDATION', 6);

define('EVSTATUS_SYNTAX_OK', 101);
define('EVSTATUS_SYNTAX_ERROR', 901);
define('EVSTATUS_MX_RECORDS_OK', 102);
define('EVSTATUS_MX_RECORDS_ERROR', 902);
define('EVSTATUS_MX_CONNECT_OK', 103);
define('EVSTATUS_MX_CONNECT_ERROR', 903);
define('EVSTATUS_MX_TRANSMISSION_OK', 104);
define('EVSTATUS_MX_TRANSMISSION_ERROR', 904);
define('EVSTATUS_EMAIL_ACCEPTED', 105);
define('EVSTATUS_EMAIL_REJECTED', 905);

class Validate_Email
{

    protected $callback   = false;
    protected $from       = 'noboby@localdomain';
    protected $helo       = 'localdomain';
    protected $mxlist     = false;
    protected $reply      = '';
    protected $socket     = false;
    protected $status     = 0;
    protected $transcript = '';


    function __construct()
    {
        $this->clearCallback();
    }


    function check($email, $syntax_only = false)
    {
        $this->mxlist = array ();
        $this->reply = '';
        $this->transcript = '';
        $domain = $this->checkEmailSyntax($email);
        $this->status = ($domain !== '' ? EVSTATUS_SYNTAX_OK : EVSTATUS_SYNTAX_ERROR);
        if (call_user_func($this->callback, EVTASK_SYNTAX_CHECK, $this->status, $email, $domain) === false || $this->status !== EVSTATUS_SYNTAX_OK || $syntax_only === true) {
            return $this->status;
        }
        if (getmxrr($domain, $host, $prio)) {
            for ($i = 0; $i < count($host); ++ $i) {
                $this->mxlist[$host[$i]] = $prio[$i];
            }
            asort($this->mxlist, SORT_NUMERIC);
            $this->status = EVSTATUS_MX_RECORDS_OK;
        } else if (($dns = dns_get_record($domain, DNS_A)) !== false && count($dns)) {
            $this->mxlist[$domain] = 0;
            $this->status = EVSTATUS_MX_RECORDS_OK;
        } else {
            $this->status = EVSTATUS_MX_RECORDS_ERROR;
        }
        if (call_user_func($this->callback, EVTASK_MX_RECORDS, $this->status, $this->mxlist, '') === false || $this->status !== EVSTATUS_MX_RECORDS_OK) {
            return $this->status;
        }

        if (Validate::verifyWhitelisted($email)) {
            $this->status = EVSTATUS_EMAIL_ACCEPTED;
            return $this->status;
        }

        $this->status = EVSTATUS_MX_CONNECT_ERROR;
        foreach ($this->mxlist as $mx => $prio) {
            if (call_user_func($this->callback, EVTASK_MX_PRE_CONNECT, $this->status, $mx, $prio) === false) {
                return $this->status;
            }
            $this->reply = '';
            $this->transcript = '';
            $this->socket_connect($mx, 25, 3);
            $this->status = ($this->socket_connected() ? EVSTATUS_MX_CONNECT_OK : EVSTATUS_MX_CONNECT_ERROR);
            if (call_user_func($this->callback, EVTASK_MX_CONNECT, $this->status, $mx, $prio) === false) {
                $this->socket_close();
                return $this->status;
            }
            if ($this->socket_connected()) {
                $response = false;
                $this->socket_timeout(5);
                $this->transcript = $this->socket_recv($code);
                if ($code === 220) {
                    $this->transcript .= $this->socket_send("HELO {$this->helo}\r\n");
                    $this->transcript .= $this->socket_recv($code);
                    if ($code === 250) {
                        $this->transcript .= $this->socket_send("MAIL FROM: <{$this->from}>\r\n");
                        $this->transcript .= $this->socket_recv($code);
                        if ($code === 250) {
                            $this->transcript .= $this->socket_send("RCPT TO: <{$email}>\r\n");
                            $this->reply = $this->socket_recv($response);
                            $this->transcript .= $this->reply;
                            $this->transcript .= $this->socket_send("QUIT\r\n");
                            $this->transcript .= $this->socket_recv($code);
                        }
                    }
                }
                $this->socket_close();
                $this->status = ($response !== false ? EVSTATUS_MX_TRANSMISSION_OK : EVSTATUS_MX_TRANSMISSION_ERROR);
                if (call_user_func($this->callback, EVTASK_MX_TRANSMISSION, $this->status, $mx, $this->transcript) === false) {
                    return $this->status;
                }
                if ($response !== false) {
                    $this->status = ($response === 250 ? EVSTATUS_EMAIL_ACCEPTED : EVSTATUS_EMAIL_REJECTED);
                    call_user_func($this->callback, EVTASK_VALIDATION, $this->status, $email, $this->reply);
                    break;
                }
            }
        }
        return $this->status;
    }


    function checkEmailSyntax($email)
    {
        if (preg_match('/^[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@((?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)$/i', $email, $match)) {
            return $match[1];
        }
        return '';
    }


    function clearCallback()
    {
        $this->callback = array (&$this, 'defaultCallback');
    }


    function getMXList()
    {
        return $this->mxlist;
    }


    function getReply()
    {
        return $this->reply;
    }


    function getStatus()
    {
        return $this->status;
    }


    function getTranscript()
    {
        return $this->transcript;
    }


    function setCallback($func)
    {
        if ($func) {
            $this->callback = $func;
        } else {
            $this->clearCallback();
        }
    }


    function setMailFrom($email)
    {
        $domain = $this->checkEmailSyntax($email);
        if ($domain === '') {
            return false;
        }
        $this->helo = $domain;
        $this->from = $email;
        return true;
    }


    protected function defaultCallback($task, $status, $object, $data)
    {
        return true;
    }


    protected function socket_close()
    {
        if (!$this->socket_connected()) {
            return false;
        }
        fclose($this->socket);
        $this->socket = false;
        return true;
    }


    protected function socket_connect($host, $port, $timeout = 0)
    {
        $this->socket_close();
        $host = (string)$host;
        $port = (int)$port;
        $timeout = (int)$timeout;
        if ($host === '' || $port < 1) {
            return false;
        }
        $this->socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
        return $this->socket_connected();
    }


    protected function socket_connected()
    {
        return ($this->socket !== false);
    }


    protected function socket_recv(&$code)
    {
        if (!$this->socket_connected()) {
            return false;
        }
        $code = 0;
        $data = '';
        while (1) {
            $buf = @fgets($this->socket);
            if ($buf === false || !preg_match('/^([0-9]+)([- ])/', $buf, $match)) {
                $code = 0;
                break;
            }
            $data .= $buf;
            $code = (int)$match[1];
            if ($match[2] !== '-') {
                break;
            }
        }
        return $data;
    }


    protected function socket_send($data)
    {
        if (!$this->socket_connected()) {
            return false;
        }
        $buf = '';
        $len = strlen($data);
        while ($len > 0) {
            $sent = @fwrite($this->socket, $data);
            if ($sent === false) {
                break;
            }
            if ($sent > 0) {
                $buf .= substr($data, 0, $sent);
                if ($sent < $len) {
                    $data = substr($data, $sent);
                }
            }
            $len -= $sent;
        }
        return $buf;
    }


    protected function socket_timeout($sec = 0, $usec = 0)
    {
        if (!$this->socket_connected()) {
            return false;
        }
        @stream_set_timeout($this->socket, $sec, $usec);
        return true;
    }
}
