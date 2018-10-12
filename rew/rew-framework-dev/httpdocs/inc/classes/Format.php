<?php

use REW\Core\Interfaces\FormatInterface;

/**
 * Format
 *
 */
class Format implements FormatInterface
{
    use REW\Traits\StaticNotStaticTrait;

    /**
     * Generate URL-Friendly Link from String
     *
     * @param string $input
     * @param string $regxp
     * @param boolean $lowercase
     * @return string
     */
    public function slugify($input, $regxp = '/[^a-zA-Z0-9_-]/', $lowercase = true)
    {
        $output = iconv('UTF-8', 'ASCII//TRANSLIT', $input);
        $output = str_replace(' ', '-', $output);
        $output = preg_replace($regxp, '', $output);
        $output = preg_replace('/(-+)/', '-', $output);
        $output = trim($output, '- ');
        if (!empty($lowercase)) {
            $output = strtolower($output);
        }
        return $output;
    }

    /**
     * Converts a string to snake case
     * @param string $input
     * @return string
     */
    public function snakeCase($input)
    {
        return strtolower(preg_replace('/(.)([A-Z])/', '$1-$2', $input));
    }

    /**
     * Converts a string to camel case
     * @param string $input
     * @return string
     */
    public function camelCase($input)
    {
        return str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $input)));
    }

    /**
     * Turn timestamp into relative date string: '4 seconds ago', '2 weeks ago'
     *
     * @param int $timestamp    UNIX Timestamp
     * @return string
     */
    public function dateRelative($timestamp)
    {
        if (strtotime('0000-00-00') - $timestamp === 0) {
            return 'never';
        }
        $timestamp = is_numeric($timestamp) ? $timestamp : strtotime($timestamp);
        $periods = array(__('second'), __('minute'), __('hour'), __('day'), __('week'), __('month'), __('year'), __('decade'));
        $lengths = array('60', '60', '24', '7', '4.35', '12', '10');
        $difference = time() - $timestamp;
        if ($difference < 0) {
            return date('F jS, Y', $timestamp); // Future Date
        }
        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
            $difference /= $lengths[$j];
        }
        $difference = round($difference);
        if ($difference != 1) {
            $periods[$j] .= 's'; // Plural
        }
        if (empty($difference)) {
            return __('just now');
        }
        return $difference . ' ' . $periods[$j] . ' ' . __('ago');
    }

    /**
     * Truncates text.
     *
     * <code>
     * Format::truncate($text, 100);
     * Format::truncate($html, 775, '&hellip;', false, true);
     * </code>
     *
     * Cuts a string to the length of $length and replaces the last characters
     * with the ending if the text is longer than length.
     *
     * @param string  $text String to truncate.
     * @param integer $length Length of returned string, including ellipsis.
     * @param string  $ending Ending to be appended to the trimmed string.
     * @param boolean $exact If false, $text will not be cut mid-word
     * @param boolean $considerHtml If true, HTML tags would be handled correctly
     * @return string Trimmed string.
     */
    public function truncate($text, $length = 100, $ending = '...', $exact = true, $considerHtml = false)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(FormatInterface::class, __FUNCTION__, func_get_args());
        }

        // Set default encoding
        mb_internal_encoding('UTF-8');

        if ($considerHtml) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = mb_strlen($ending);
            $open_tags = array();
            $truncate = '';
            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                        // do nothing
                    // if tag is a closing tag (f.e. </b>)
                    } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        // delete tag from $open_tags list
                        $pos = array_search($tag_matchings[1], $open_tags);
                        if ($pos !== false) {
                            unset($open_tags[$pos]);
                        }
                    // if tag is an opening tag (f.e. <b>)
                    } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                        // add tag to the beginning of $open_tags list
                        array_unshift($open_tags, mb_strtolower($tag_matchings[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matchings[1];
                }
                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                if ($total_length+$content_length > $length) {
                    // the number of characters which are left
                    $left = $length - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($entity[1]+1-$entities_length <= $left) {
                                $left--;
                                $entities_length += mb_strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= mb_substr($line_matchings[2], 0, $left+$entities_length);
                    // maximum lenght is reached, so get off the loop
                    break;
                } else {
                    $truncate .= $line_matchings[2];
                    $total_length += $content_length;
                }
                // if the maximum length is reached, get off the loop
                if ($total_length >= $length) {
                    break;
                }
            }
        } else {
            $text = $this->stripTags($text);
            if (mb_strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = mb_substr($text, 0, $length - mb_strlen($ending));
            }
        }
        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = mb_strrpos($truncate, ' ');
            $brpos = mb_strrpos($truncate, '>')+1;
            if (isset($spacepos) || isset($brpos)) {
                // ...and cut the text in this position
                $trpos = ($spacepos > $brpos) ? $spacepos : $brpos;
                $truncate = mb_substr($truncate, 0, $trpos);
            }
        }
        if ($considerHtml) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }
        // add the defined ending to the text
        $truncate .= $ending;
        return $truncate;
    }

    /**
     * Remove HTML tags, including invisible text such as style and
     * script code, and embedded objects.  Add line breaks around
     * block-level tags to prevent word joining after tag removal.
     *
     * @param string $text    HTML String
     * @param string $keep    Allowable HTML Tags
     */
    public function stripTags($text, $keep = null)
    {
        $text = preg_replace(array(
            // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ), array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
        ), $text);
        return strip_tags($text, $keep);
    }

    /**
     * Pick Plural Word or Single Word based on Count
     *
     * @param int $count        Count to Decide
     * @param string $plural    Pluralized Word
     * @param string $single    Singled Word
     * @return string Either $plural or $single
     */
    public function plural($count, $plural, $single)
    {
        if ($count == 1) {
            return $single;
        } else {
            return $plural;
        }
    }

    /**
     * Recusive htmlspecialchars
     * @param string|array $data
     * @param int $flags
     * @param string $encoding
     * @param bool $double_encode
     * @return string|array
     */
    public function htmlspecialchars($data, $flags = ENT_COMPAT, $encoding = 'UTF-8', $double_encode = false)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(FormatInterface::class, __FUNCTION__, func_get_args());
        }

        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->htmlspecialchars($v, $flags, $encoding, $double_encode);
            }
        } else {
            $data = htmlspecialchars($data, $flags, $encoding, $double_encode);
        }
        return $data;
    }

    /**
     * Recusive Trim
     *
     * @param string|array $input
     * @param string $character_mask
     * @return string|array
     */
    public function trim($input, $character_mask = " \t\n\r\0\x0B")
    {
        if (!is_array($input)) {
            return trim($input, $character_mask);
        }
        return array_map(array(__CLASS__, __METHOD__), $input);
    }

    /**
     * Properly Uppercase First Character of Names
     *
     * @param string $string
     * @return string
     */
    public function ucnames($string)
    {
        $format = function ($regex) {
            $word = strtolower($regex[1]);
            if ($word == 'de') {
                return str_replace($regex[1], $word, $regex[0]);
            }
            $word = ucfirst($word);
            if (substr($word, 1, 1) == "'") {
                if (substr($word, 0, 1) == 'D') {
                    $word = strtolower($word);
                }
                $next = substr($word, 2, 1);
                $next = strtoupper($next);
                $word = substr_replace($word, $next, 2, 1);
            }
            $word = preg_replace_callback('/
				(?: ^ | \\b )         # assertion: beginning of string or a word boundary
				( O\' | Ma?c | Fitz)  # attempt to match Irish surnames
				( [^\W\d_] )          # match next char; we exclude digits and _ from \w
			/x', function ($match) {
                return $match[1] . strtoupper($match[2]);
}, $word);
            return str_replace($regex[1], $word, $regex[0]);
        };
        $string = preg_replace_callback('/(?:^|\\s)([\\w\']+)\\s?/s', $format, $string);
        return $string;
    }

    /**
     * Apply number_format() if value is numeric - otherwise, return it
     *
     * @param mixed $value The number being formatted.
     * @param int $decimals Sets the number of decimal points.
     * @param string $dec_point Sets the separator for the decimal point.
     * @param string $thousands_sep Sets the thousands separator.
     * @return string A formatted version of number.
     */
    public function number($value, $decimals = 0, $dec_point = '.', $thousands_sep = ',')
    {
        return is_numeric($value) ? number_format($value, $decimals, $dec_point, $thousands_sep) : $value;
    }

    /**
     * Returns a formatted-for-humans number - eg: 500k, 1M, 1.5B
     *
     * @param int $value
     * @return string
     */
    public function shortNumber($value)
    {
        $value = (0 + str_replace(',', '', $value)); // remove formatting
        if (!is_numeric($value)) {
            return false; // require number
        }
        $t = ($value / 1000000000000); // trillion
        $b = ($value / 1000000000); // billions
        $m = ($value / 1000000); // millions
        $k = ($value / 1000); // thousands
        if ($t >= 1) {
            if (is_float($t) && $t < 100) {
                return round($t, 1) . 'T';
            }
            return round($t) . 'T';
        } else if ($b >= 1) {
            if (is_float($b) && $b < 100) {
                return round($b, 1) . 'B';
            }
            return round($b) . 'B';
        } else if ($m >= 1) {
            if (is_float($m) && $m < 100) {
                return round($m, 1) . 'M';
            }
            return round($m) . 'M';
        } else if ($k >= 1) {
            if (is_float($k) && $k < 100) {
                return round($k, 1) . 'k';
            }
            return round($k) . 'k';
        }
        return is_float($value) ? number_format($value, 2) : number_format($value);
    }

    /**
     * Turn bytes into a human-readable string
     *
     * @param int $bytes
     * @return string
     */
    public function filesize($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }

    /**
     * Turn a Float into a fraction
     *
     * @param string $value
     * @return mixed
     */
    public function fraction($value)
    {
        // Only Numeric Values Accepted
        if (!is_numeric($value)) {
            return $value;
        }
        list ($num, $dec) = explode('.', (float) $value);
        if (!empty($dec)) {
            $dec = rtrim($dec, 0);
            $frac = false;
            if ($dec === '25') {
                $frac = '&frac14;';
            }
            if ($dec === '5') {
                $frac = '&frac12;';
            }
            if ($dec === '75') {
                $frac = '&frac34;';
            }
            $dec = !empty($frac) ? $frac : '.' . $dec;
        }
        if ($num == 0) {
            $num = '';
        }
        return $num . $dec;
    }

    /**
     * Use Google's URL Shortener API to Shorten a URL (for use in SMS Messages)
     * @param string $url URL to Shorten
     * @param array $errors If present, error report will be appended to collection
     * @return string Shortened URL on success, Original URL on failure.
     */
    public function tinyUrl($url, &$errors = array())
    {

        // Make cURL Request
        $json = Util_Curl::executeRequest('https://www.googleapis.com/urlshortener/v1/url', array(), Util_Curl::REQUEST_TYPE_POST, array(
            CURLOPT_POSTFIELDS => json_encode(array('longUrl' => $url), JSON_FORCE_OBJECT),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json')
        ));

        // Response Info
        $info = Util_Curl::info();

        // Require 200 Response
        if ($info['http_code'] == 200) {
            $data = json_decode($json, true);
            return $data['id'];

        // Could Not Shorten URL
        } else {
            // Report to Errors
            $errors[] = 'URL could not be shortened: ' . $url;

            // Return Original URL
            return $url;
        }
    }

    /**
     * Get PHPThumb Resize URL for Image
     * @param string $url
     * @param string $size
     * @return string
     */
    public function thumbUrl($url, $size)
    {
        $rootUrl = Settings::getInstance()->URLS['URL'];
        $pattern = '#^http(s)?://(www\.)?#i';
        if (preg_match($pattern, $url)) {
            return preg_replace_callback($pattern, function ($matches) use ($rootUrl, $size) {
                $ssl = ($matches[1] ? 'ssl/' : '');
                return sprintf('%sthumbs/%s/%s', $rootUrl, $size, $ssl);
            }, $url);
        } else {
            return sprintf('%sthumbs/%s%s', $rootUrl, $size, $url);
        }
    }

    /**
     * Un-Serialize PHP Data (UTF-8 Encoding Fixed)
     *
     * @link http://www.php.net/manual/en/function.unserialize.php#93606
     * @param string $input
     * @return mixed
     */
    public function unserialize($input)
    {
        $output = unserialize($input);
        if ($output === false) {
            $input = preg_replace_callback('!(?<=^|;)s:(\d+)(?=:"(.*?)";(?:}|a:|s:|b:|d:|i:|o:|N;))!s', function ($match) {
                return 's:' . strlen($match[2]);
            }, $input);
            return unserialize($input);
        }
        return $output;
    }

    /**
     * Convert binary string back into a guid
     * @param binary $bin
     * @return string
     */
    public function toGuid($bin)
    {
        $guid = '00000000-0000-0000-0000-000000000000';
        if (!empty($bin)) {
            $guid = strtoupper(
                bin2hex(substr($bin, 3, 1)).
                bin2hex(substr($bin, 2, 1)).
                bin2hex(substr($bin, 1, 1)).
                bin2hex(substr($bin, 0, 1)).
                '-'.
                bin2hex(substr($bin, 5, 1)).
                bin2hex(substr($bin, 4, 1)).
                '-'.
                bin2hex(substr($bin, 7, 1)).
                bin2hex(substr($bin, 6, 1)).
                '-'.
                bin2hex(substr($bin, 8, 2)).
                '-'.
                bin2hex(substr($bin, 10, 6))
            );
        }
        return $guid;
    }

    /**
     * Format input as phone number
     * @param string $input
     * @return string
     */
    public function phone($input)
    {
        if (!$this instanceof self) {
            return self::callInstanceMethod(FormatInterface::class, __FUNCTION__, func_get_args());
        }

        $digits = preg_replace('/[^0-9]/', '', $input);
        $length = strlen($digits);
        if ($length === 7) {
            return preg_replace('/([0-9]{3})([0-9]{4})/', '$1-$2', $digits);
        } else if ($length === 10) {
            return preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '($1) $2-$3', $digits);
        } else if ($length === 11) {
            return preg_replace('/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/', '$1 ($2) $3-$4', $digits);
        }
        return $this->htmlspecialchars($input);
    }
}
