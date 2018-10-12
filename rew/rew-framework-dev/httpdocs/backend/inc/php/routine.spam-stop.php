<?php

/**
 * Convert a string to mixed-case on word boundaries.
 * @param $string
 */
function uc_all($string)
{
    $temp = preg_split('/(\W)/', str_replace("_", "-", $string), -1, PREG_SPLIT_DELIM_CAPTURE);
    foreach ($temp as $key => $word) {
        $temp[$key] = ucfirst(strtolower($word));
    }
    return join('', $temp);
}

/**
 * Replace getallheaders in FastCGI enviroment
 */
if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5)=="HTTP_") {
                $key=str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
                $out[$key]=$value;
            } else {
                $out[$key]=$value;
            }
        }
        return $out;
    }
}

/**
 *
 * @param unknown_type $package
 */
function checkForSpam(&$package)
{

    /* Stip the headers */
    $find = array("bcc:", "Content-Type:", "cc:", "to:");
    foreach ($find as $needle) {
        if (stripos($package['email'], $needle)) {
            return "sm6csexh";
        }
        if (stripos($package['fname'], $needle)) {
            return "jel0larx";
        }
        if (stripos($package['lname'], $needle)) {
            return "nig2syci";
        }
        if (stripos($package['subject'], $needle)) {
            return "kNZuu9n0";
        }
    }

    // check for known bad addresses
    $bad_addy = array('zzz@hotmail.com');
    foreach ($bad_addy as $addy) {
        if (stripos($package['email'], $addy)) {
            return $package['email'];
        }
    }

    // Content-Range is a response header, not a request header
    if (array_key_exists('Content-Range', $package['headers_mixed'])) {
        return '7d12528e';
    }

    // Lowercase via is used by open proxies/referrer spammers
    if (array_key_exists('via', $package['headers'])) {
        return "9c9e4979";
    }

    // pinappleproxy is used by referrer spammers
    if (array_key_exists('Via', $package['headers_mixed'])) {
        if (stripos($package['headers_mixed']['Via'], "pinappleproxy") !== false || stripos($package['headers_mixed']['Via'], "PCNETSERVER") !== false || stripos($package['headers_mixed']['Via'], "Invisiware") !== false) {
            return "939a6fbb";
        }
    }

    if (array_key_exists('Connection', $package['headers_mixed'])) {
        // Connection: keep-alive and close are mutually exclusive
        if (preg_match('/\bKeep-Alive\b/i', $package['headers_mixed']['Connection']) && preg_match('/\bClose\b/i', $package['headers_mixed']['Connection'])) {
            return "a52f0448";
        }
        // Close shouldn't appear twice
        if (preg_match('/\bclose,\s?close\b/i', $package['headers_mixed']['Connection'])) {
            return "a52f0448";
        }
        // Keey-Alive shouldn't appear twice either
        if (preg_match('/\bkeep-alive,\s?keep-alive\b/i', $package['headers_mixed']['Connection'])) {
            return "a52f0448";
        }
    }

    // Headers which are not seen from normal user agents; only malicious bots
    if (array_key_exists('X-Aaaaaaaaaaaa', $package['headers_mixed']) || array_key_exists('X-Aaaaaaaaaa', $package['headers_mixed'])) {
        return "b9cc1d86";
    }

    if (array_key_exists('Proxy-Connection', $package['headers_mixed'])) {
        return "b7830251";
    }

    // Specific checks
    $ua = $package['headers_mixed']['User-Agent'];

    // MSIE checks
    if (stripos($ua, "MSIE") !== false) {
        $package['is_browser'] = true;
    } elseif (stripos($ua, "Konqueror") !== false) {
        $package['is_browser'] = true;
    } elseif (stripos($ua, "Opera") !== false) {
        $package['is_browser'] = true;
    } elseif (stripos($ua, "Safari") !== false) {
        $package['is_browser'] = true;
    } elseif (stripos($ua, "Mozilla") !== false && stripos($ua, "Mozilla") == 0) {
        $package['is_browser'] = true;
    }

    // Stick all the form fields into one string
    $form_content = implode(" ", $package['request_entity']);

    // check for too many links
    preg_match_all("/(https?:\/\/)([\w\.\/-]+)/", $form_content, $match);
    foreach ($match[1] as $link) {
        $package['link-limit']--;
    }

    if ($package['link-limit'] < 0) {
        return 'vir9bumo';
    }

    // check for too many special chars
    preg_match_all("/([\x7f-\xff]{" . $package['sp_char_limit'] . "})/", $form_content, $match);

    if (count($match[1]) > 0) {
        return 'wh0glvee';
    }
}

// The testing code
$headers = getallheaders();

$headers_mixed = array();
foreach ($headers as $h => $v) {
    $headers_mixed[uc_all($h)] = $v;
}

// We use these frequently. Keep a copy close at hand.
$ip = $_SERVER['REMOTE_ADDR'];
$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$server_protocol = $_SERVER['SERVER_PROTOCOL'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// Reconstruct the HTTP entity, if present.
$request_entity = array();
if (!strcasecmp($request_method, "POST") || !strcasecmp($request_method, "PUT")) {
    foreach ($_POST as $h => $v) {
        $request_entity[$h] = $v;
    }
}

$link_limit = 2; // number of allowable links
$special_char_limit = 3; // num of special chars in a row to look for

$package = array(
    'ip' => $ip,
    'headers' => $headers,
    'headers_mixed' => $headers_mixed,
    'request_method' => $request_method,
    'request_uri' => $request_uri,
    'server_protocol' => $server_protocol,
    'request_entity' => $request_entity,
    'user_agent' => $user_agent,
    'is_browser' => false,
    'email' => $email,
    'fname' => $first_name,
    'lname' => $last_name,
    'subject' => $subject,
    'link-limit' => $link_limit,
    'sp_char_limit' => $special_char_limit);
