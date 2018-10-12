<?php

/**
 * Check if URL Exists
 *
 * @param string $url
 *  - website address
 * @return int
 *  - 1 for a valid page
 *  - 2 for a timed out page
 *  - 3 for an invalid page
 * @example
 *  url_exists('http://www.google.ca/');
 */
function url_exists($url)
{

    /* this function will return:
    1 for a valid page
    2 for a timed out page
    3 for an invalid page
    */

    $parts = parse_url($url);
    if (!$parts) {
        return 3; /* the URL was seriously wrong */
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    /* try to follow redirects */
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    /* timeout after the specified number of seconds. assuming that this script runs
    on a server, 10 seconds should be plenty of time to verify a valid URL.  */
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    /* don't download the page, just the header (much faster in this case) */
    curl_setopt($ch, CURLOPT_HEADER, true);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    /*  get the LAST status code from HTTP headers */
    if (preg_match_all('/HTTP\/1\.\d+\s+(\d+)/', $response, $matches)) {
        $code = intval($matches[1][(count($matches[1]) - 1)]);
    } else {
        if (stristr($error, 'operation timed out')) {
            //timed out
            return 2;
        } else {
            //not found
            return 3;
        }
    }

    /* see if code indicates success */
    if (($code >= 200) && ($code < 400)) {
        // success
        return 1;
    } else {
        // not found
        return 3;
    }
}

/**
 *
 */
function pingback_autodiscover($url)
{
    $url = '';
    if (preg_match('/\<link rel="pingback" href="([^"]+)" ?\/?\>/i', $content, $matches)) {
        return $matches[1];
    }
}

/**
 * Check if $url1 contains a link to $url2
 *
 * @param string $url1
 *  - page to search
 * @param string $url2
 *  - link to search form
 * @return boolean
 *  - $url1 contains a link to $url2
 */
function url_contains_link($url1, $url2)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION,  true);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);

    $html = curl_exec($ch);
    curl_close($ch);

    if (!$html) {
        return false;
    }

    /* Remove HTML comments and their contents */
    $html = preg_replace('/<!--.*-->/i', '', $html);

    /* Extract all links */
    $regexp ='/(<a[\s]+[^>]*href\s*=\s*[\"\']?)([^\'\" >]+)([\'\"]+[^<>]*>)/i';
    if (!preg_match_all($regexp, $html, $matches, PREG_SET_ORDER)) {
        return false; /* No links on page */
    }

    /* base url, used to change relavent links */
    $parts = parse_url($url1);
    $base  = $parts['scheme'] . '://' . $parts['host'];

    /* Check each link */
    foreach ($matches as $match) {
        /* Skip links that contain rel=nofollow */
        if (preg_match('/rel\s*=\s*[\'\"]?nofollow[\'\"]?/i', $match[0])) {
            continue;
        }
        /* If URL = backlink_url, we've found the backlink */
        //echo "($match[2] == $url2)" . "\n";
        /* update relative links */
        $match[2] = preg_match("#^/#", $match[2]) ? $base . $match[2] : $match[2];
        if ($match[2] == $url2) {
            return true;
        }
    }

    return false;
}

function url_meta_info($source_url, $target_url)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $source_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION,  true);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);

    $html = curl_exec($ch);
    curl_close($ch);

    if (!$html) {
        return false;
    }

    /* Remove HTML comments and their contents */
    $html = preg_replace('/<!--.*-->/i', '', $html);

    /* Collect Meta Information */
    if (preg_match('/<title>(.*)<\/title>/', $html, $pt)) {
        $info['page_title'] = $pt[1];
    }

    if (preg_match('/<meta name="description" content="(.*)" ?\/>/', $html, $mtd)) {
        $info['meta_tag_desc'] = $mtd[1];
    }

    if (preg_match('/<meta name="keywords" content="(.*)" ?\/>/', $html, $mtk)) {
        $info['meta_tag_keywords'] = $mtk[1];
    }

    $html = strip_tags($html, '<a>');
    $html = explode("\n\n", $html);

    $target_url = str_replace('http://' . $_SERVER['HTTP_HOST'], "", $target_url);

    foreach ($html as $line) {
        if (strpos($line, $target_url) !== false) {
            preg_match("|<a[^>]+?" . preg_quote($target_url, '/') . "[^>]*>([^>]+?)</a>|", $line, $link);
            if (empty($link)) {
                continue;
            }
            $excerpt = preg_replace('|\</?excerpt\>|', '', $line);
            if (strlen($link[1]) > 100) {
                $link[1] = substr($link[1], 0, 100) . '...';
            }
            $marker = '<excerpt>' . $link[1] . '</excerpt>';
            $excerpt = str_replace($link[0], $marker, $excerpt);
            $excerpt = strip_tags($excerpt, '<excerpt>');
            $excerpt = preg_replace("|.*?\s(.{0,100}" . preg_quote($marker, '/') . ".{0,100})\s.*|s", '$1', trim($excerpt));
            $excerpt = strip_tags($excerpt);
            break;
        }
    }
    //$info['excerpt'] = '[..]' . $excerpt . '[..]';
    $info['excerpt'] = $excerpt;

    if (!empty($info)) {
        return $info;
    } else {
        return false;
    }
}
