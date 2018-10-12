<?php

/**
 * Process Pingback for Blog Entry
 *
 * @param array $entry
 *  - blog_entry database row
 */
function pingback($entry)
{

    if ($entry['published'] == 'false') {
        return;
    }

    /* Pins */
    $pings = array();

    /* Look through all Links */
    preg_match_all("/a[\s]+[^>]*?href[\s]?=[\s\"\']+(.*?)[\"\']+.*?>"."([^<]+|.*?)?<\/a>/", $entry['body'], $matches);
    $matches[1][] = $entry['link_url1'];
    $matches[1][] = $entry['link_url2'];
    $matches[1][] = $entry['link_url3'];
    foreach ($matches[1] as $link) {
        $link = preg_match("#^/blog/#i", $link) ? 'http://' . $_SERVER['HTTP_HOST'] . $link : $link;

        /* Check Link */
        $parts = parse_url($link);
        if (!$parts) {
            continue;
        }

        /* Send Ping */
        $ping = pingback_ping(sprintf(URL_BLOG_ENTRY, $entry['link']), $link);

        if (!empty($ping)) {
            $pings[] = $ping;
        }
    }

    return implode('<br />', $pings);
}

function pingback_ping($source_url, $target_url)
{

    /* Validate Target URL */
    $parts = parse_url($target_url);

    if (!isset($parts['scheme'])) {
        return; //return "pingback_ping: failed to get url scheme [". $target_url ."]<br />\n";
    }

    if ($parts['scheme'] != 'http') {
        return; //return "pingback_ping: url scheme is not http [". $target_url ."]<br />\n";
    }

    if (!isset($parts['host'])) {
        return; //return "pingback_ping: could not get host [". $target_url ."]<br />\n";
    }

    /* Build Target URL */
    $host = $parts['host'];
    $port = isset($parts['port'])     ? $parts['port'] : 80;
    $path = isset($parts['path'])     ? $parts['path'] : '/';
    $path = isset($parts['query'])    ? $path . '?' . $parts['query']    : $path;
    $path = isset($parts['fragment']) ? $path . '#' . $parts['fragment'] : $path;

    /* HTTP HEAD Request */
    $fp = @fsockopen($host, $port);
    if ($fp) {
        fwrite($fp, "HEAD $path HTTP/1.0\r\nHost: $host\r\n\r\n");
        $response = fread($fp, (1024 * 10));
        fclose($fp);

        /* Discover Pingback Header */
        preg_match("/X-Pingback: (\S+)/i", $response, $matches);
        if (isset($matches[1])) {
            $pburl = $matches[1];
        } else {
            /* HTTP GET Request */
            $fp = @fsockopen($host, $port);
            if ($fp) {
                fwrite($fp, "GET $path HTTP/1.0\r\nHost: $host\r\n\r\n");
                $response = fread($fp, (1024 * 10));
                fclose($fp);

                /* Discover Pingback HTTP Link */
                preg_match("/<link rel=\"pingback\" href=\"([^\"]+)\" ?\/?>/i", $response, $matches);
                $pburl = $matches[1];
            } else {
                return "Pingback to " . $target_url . " failed with error message: Could not connect to host<br >\n";
            }
        }

        /* Validate Pingback Server */
        $parts = parse_url($pburl);

        if (empty($pburl)) {
            return; //return "Could not get pingback url from [$target_url].<br />\n";
        }

        if (!isset($parts['scheme'])) {
            return; //return "pingback_ping: failed to get pingback url scheme [".$pburl."]<br />\n";
        }

        if ($parts['scheme'] != 'http') {
            return; //return "pingback_ping: pingback url scheme is not http[".$pburl."]<br />\n";
        }

        if (!isset($parts['host'])) {
            return; //return "pingback_ping: could not get pingback host [".$pburl."]<br />\n";
        }

        /* Build Pingback Server URL */
        $host = $parts['host'];
        $port = isset($parts['port'])     ? $parts['port'] : 80;
        $path = isset($parts['path'])     ? $parts['path'] : '/';
        $path = isset($parts['query'])    ? $path . '?' . $parts['query']    : $path;
        $path = isset($parts['fragment']) ? $path . '#' . $parts['fragment'] : $path;

        // Include XML-RPC Library
        require_once Settings::getInstance()->DIRS['LIB'] . 'xmlrpc/xmlrpc.inc.php';
        require_once Settings::getInstance()->DIRS['LIB'] . 'xmlrpc/xmlrpcs.inc.php';
        require_once Settings::getInstance()->DIRS['LIB'] . 'xmlrpc/xmlrpc_wrappers.inc.php';

        /* XML-RPC Message Response */
        $message = new xmlrpcmsg("pingback.ping", array (
                       new xmlrpcval($source_url, "string"),
                       new xmlrpcval($target_url, "string")
                   ));

        /* XML-RPC Client */
        $client = new xmlrpc_client($path, $host, $port);

        /* Set Client Options */
        $client->setRequestCompression(false);
        $client->setAcceptedCompression(false);
        //$client->setDebug(2);

        /* Send XML-RPC Response */
        $response = $client->send($message, 5);

        if (!$response->faultCode() || ($response->faultCode() == 6)) {
            return "Pingback to " . $target_url . " succeeded.<br >\n";
        } else {
            //$err = "code ". $response->faultCode() . " message " . $response->faultString();
            $err = "message: <br /><em>" . $response->faultString() . '</em>';
            return "Pingback to " . $target_url . " failed with error " . $err . "<br >\n";
        }
    } else {
        return "Pingback to " . $target_url . " failed with error message: Could not connect to host<br >\n";
    }
}
