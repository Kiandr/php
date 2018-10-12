<?php

/**
 * Format_HTML
 * <code>
 * <?php
 *
 *  $input = 'Check us out at www.realestatewebmasters.com?refid=abc123'
 *      . ' or email us at sales@realestatewebmasters.com!';
 *  $format = new Format_HTML($input);
 *  $format->removeUrlParams(array('refid'));
 *  $format->convertUrlsAndEmailsToLinks();
 *  echo $format->getOutput();
 *  // Check us out at <a href="http://www.realestatewebmasters.com" target="_blank">www.realestatewebmasters.com</a>
 *  // or email us at <a href="mailto:sales@realestatewebmasters.com">sales@realestatewebmasters.com</a>!
 *
 * ?>
 * </code>
 * @package Format
 */
class Format_HTML
{

    /**
     * HTML to parse
     * @var string
     */
    protected $in;

    /**
     * Formatted Output
     */
    protected $out;

    /**
     * Initialize formatted
     * @param string $html
     */
    public function __construct($html)
    {
        $this->in = (string) $html;
        $this->out = (string) $html;
    }

    /**
     * Get original HTML input
     * @return string
     */
    public function getInput()
    {
        return $this->in;
    }

    /**
     * Get formatted HTML output
     * @return string
     */
    public function getOutput()
    {
        return $this->out;
    }

    /**
     * Turn all urls/emails into hyperlinks
     * @return string
     */
    public function convertUrlsAndEmailsToLinks()
    {
        $this->convertUrlsToLinks();
        $this->convertFuzzyUrlsToLinks();
        $this->convertEmailsToLinks();
        return $this->getOutput();
    }

    /**
     * Replace URLs with Links
     * @return string
     */
    public function convertUrlsToLinks()
    {
        $urls = $this->extractUrls();
        $this->out = str_replace($urls, array_map(function ($url) {
            return '<a href="' . $url . '" target="_blank">' . $url . '</a>';
        }, $urls), $this->out);
        return $this->getOutput();
    }

    /**
     * Replace Fuzzy URLs with Links
     * @return string
     */
    public function convertFuzzyUrlsToLinks()
    {
        $urls = $this->extractFuzzyUrls();
        $this->out = str_replace($urls, array_map(function ($url) {
            return '<a href="http://' . $url . '" target="_blank">' . $url . '</a>';
        }, $urls), $this->out);
        return $this->getOutput();
    }

    /**
     * Replace Emails with mailto: Links
     * @return string-
     */
    public function convertEmailsToLinks()
    {
        $emails = $this->extractEmails();
        $this->out = str_replace($emails, array_map(function ($email) {
            return '<a href="mailto:' . $email . '">' . $email . '</a>';
        }, $emails), $this->out);
        return $this->getOutput();
    }

    /**
     * Remove known query params from matching URL
     * @param array $removeParams
     * @return string
     */
    public function removeUrlParams(array $removeParams = array())
    {
        $urls = $this->extractUrls() + $this->extractFuzzyUrls();
        if (!empty($urls)) {
            foreach ($urls as $url) {
                $query = parse_url($url, PHP_URL_QUERY);
                if (!empty($query)) {
                    parse_str($query, $params);
                    if (!empty($params)) {
                        foreach ($params as $k => $k) {
                            if (in_array($k, $removeParams)) {
                                unset($params[$k]);
                            }
                        }
                        $replace = strtok($url, '?') . ($params ? '?' . http_build_query($params) : '');
                        $this->out = preg_replace('#' . preg_quote($url) . '#', $replace, $this->out, 1);
                    }
                }
            }
        }
        return $this->getOutput();
    }

    /**
     * Matches an "xxxx://yyyy" URL at the start of a line, or after a space.
     *  - xxxx can only be alpha characters.
     *  - yyyy is anything up to the first space, newline, comma, double quote or <
     * @return array
     */
    public function extractUrls()
    {
        if (preg_match_all("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", $this->out, $matches)) {
            return Format::trim($matches[0]);
        }
        return array();
    }

    /**
     * Matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing
     *  - Must contain at least 2 dots. xxxx contains either alphanum, or "-" zzzz is optional..
     *  - Will contain everything up to the first space, newline, comma, double quote or <.
     * @return array
     */
    public function extractFuzzyUrls()
    {
        if (preg_match_all("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", $this->out, $matches)) {
            return Format::trim($matches[0]);
        }
        return array();
    }

    /**
     * Matches an email@domain type address at the start of a line, or after a space.
     *  - Only the followed chars are valid; alphanums, "-", "_" and or ".".
     * @return array
     */
    public function extractEmails()
    {
        if (preg_match_all("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", $this->out, $matches)) {
            return Format::trim($matches[0]);
        }
        return array();
    }
}
