<?php

/**
 * Locale
 *
 */
class Locale {

    /**
     * Language Settings
     * @var array
     */
    private static $lang = array(
        'en-CA' => array(
            'Favorite' => 'Favourite',
            'Favorites' => 'Favourites',
            'favorite' => 'favourite',
            'favorites' => 'favourites',
            'Neighbor' => 'Neighbour',
            'neighbor' => 'neighbour',
            'Neighborhood' => 'Neighbourhood',
            'Neighborhoods' => 'Neighbourhoods',
            'neighborhood' => 'neighbourhood',
            'neighborhoods' => 'neighbourhoods',
            'State' => 'Province',
            'state' => 'province',
            'Zip Code' => 'Postal Code',
            'Zip Codes' => 'Postal Codes',
            'ZIP CODE' => 'POSTAL CODE',
            'ZIP' => 'Postal Code',
            'Zip' => 'Postal Code',
            'zip' => 'postal code',
        ),
        'en-US' => array(
        )
    );

    /**
     * Spell a word correctly in another language
     *
     * @param string $str    The string to translate
     * @return string        Correct spelling
     */
    public static function spell($str) {
        return (empty(self::$lang[Settings::getInstance()->LANG][$str])) ? $str : self::$lang[Settings::getInstance()->LANG][$str];
    }

    /**
     * Create JS class for localizing
     *
     * @return string    Javascript Code
     */
    public static function toJS() {
    	$lang = self::$lang[Settings::getInstance()->LANG];
        ob_start();
?>
var Locale = {
	'lang' : '<?=Settings::getInstance()->LANG; ?>',
	'spell' : function (str) {
	    var words = [];
		<?php if (!empty($lang)) foreach ($lang as $key => $value) echo "words['" . addslashes($key) . "'] = '" . addslashes($value) . "';" . PHP_EOL; ?>
	    if (typeof(words[str]) != 'undefined') {
	        return words[str];
	    } else {
	        return str;
	    }
	}
};
<?php

        return ob_get_clean();

    }
}
