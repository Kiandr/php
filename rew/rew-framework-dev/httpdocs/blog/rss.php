<?php

// Require SQL Connect
require_once __DIR__ . '/../sql_connect.php';

// Require Blog Configuration
require_once __DIR__ . '/common.inc.php';

// Require RSS Functions
require_once __DIR__ . '/inc/php/functions/funcs.RSS.php';

// Blog Not Installed, Re-Directed to Homepage
if (empty(Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'])) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 NOT FOUND', true);
    exit;
}

// Blog Database
$db = DB::get('blog');

// Query Data
$query_where = array();
$query_param = array();

// Agent Sub-Domain
if (Settings::getInstance()->SETTINGS['agent'] != 1) {
    $query_where[] = '`agent` = :agent';
    $query_param['agent'] = Settings::getInstance()->SETTINGS['agent'];
}

// Archive RSS
if (!empty($_GET['year']) && !empty($_GET['month'])) {
    $query_where[] = "DATE_FORMAT(`timestamp_published`, '%Y-%m') = :date";
    $query_param['date'] = date('Y-m', strtotime($_GET['year'] . '-' . $_GET['month'] . '-01'));
}

// Search by Author
if (!empty($_GET['author'])) {
    $author = $db->prepare("SELECT `id` FROM `agents` WHERE REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(CONCAT(`first_name`, ' ', `last_name`), '.', ''), '/', ''), ')', ''), '(', ''), '  ', ' ') LIKE REPLACE(:author, '-', ' ');");
    $author->execute(array('author' => '%' . $_GET['author'] . '%'));
    $author = $author->fetchColumn();
    if (!empty($author)) {
        $query_where[] = '`agent` = :author';
        $query_param['author'] = $author;
    }
}

// Search by Category
if (!empty($_GET['category'])) {
    $category = $db->prepare("SELECT `link` FROM `" . TABLE_BLOG_CATEGORIES . "` WHERE `link` = :category;");
    $category->execute(array('category' => $_GET['category']));
    $category = $category->fetchColumn();
    if (!empty($category)) {
        $query_where[] = 'FIND_IN_SET(:category, `categories`)';
        $query_param['category'] = $category;
    }
}

// Search by Tag
if (!empty($_GET['tag'])) {
    $tag = $db->prepare("SELECT `title` FROM `" . TABLE_BLOG_TAGS . "` WHERE `link` = :tag;");
    $tag->execute(array('tag' => $_GET['tag']));
    $tag = $tag->fetchColumn();
    if (!empty($tag)) {
        $query_where[] = 'FIND_IN_SET(:tag, `tags`)';
        $query_param['tag'] = $tag;
    }
}

// SQL WHERE
$query_where = !empty($query_where) ? ' AND ' . implode(' AND ', $query_where) : '';

// SQL LIMIT
$query_limit = !empty($_GET['limit']) && is_numeric($_GET['limit']) ? " LIMIT " . intval($_GET['limit']) : " LIMIT 10";

// SQL ORDER
$query_order = " ORDER BY `timestamp_published` DESC";

// Search Blog Entries
$entries = $db->prepare("SELECT * FROM `" . TABLE_BLOG_ENTRIES . "` WHERE `published` = 'true'" . $query_where . $query_order . $query_limit . ";");
$entries->execute($query_param);

// Link to Self
$self = Settings::getInstance()->SETTINGS['URL_RAW'] . $_SERVER['REQUEST_URI'];

// Channel Link
$link = substr($self, 0, strpos($self, '/rss') + 1);

// Replace Entities
$search  = array('&reg;', '&uuml;', '&deg;', '&eacute;', '\x96', '&ndash;', '&rsquo;', '&039;');
$replace = array('', '', '', '', '', '', '', "'");

// Codes 0–31 and 127 are non-printing control characters
// These are not valid UTF-8 - Remove them
// Values grabbed from here http://www.danshort.com/ASCIImap/ [^]
function correctSGML($string)
{
    $regex = "\x01|\x02|\x03|\x04|\x05|\x06|\x07|\x08|\x15|\x16|\x21|\x22|\x23|\x24|\x25|\x27|\x28|\x29";
    return mb_ereg_replace($regex, "", $string);
}

// Send as XML
header('Content-Type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL;

?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <atom:link href="<?=makeSafeEntities($self); ?>" rel="self" type="application/rss+xml" />
        <title><?=makeSafeEntities($blog_settings['blog_name']); ?></title>
        <link><?=$link; ?></link>
        <description><?=!empty($blog_settings['meta_tag_desc']) ? makeSafeEntities($blog_settings['meta_tag_desc']) : ''; ?></description>
<?php

// Process Blog Entries
$find_author = $db->prepare("SELECT `a`.*, `t`.`time_diff`, `t`.`daylight_savings` FROM `agents` `a` LEFT JOIN `" . LM_TABLE_TIMEZONES . "` `t` ON `a`.`timezone` = `t`.`id` WHERE `a`.`id` = :agent;");
while ($entry = $entries->fetch()) {
    // Select Author
    $find_author->execute(array('agent' => $entry['agent']));
    $author = $find_author->fetch();

    // Format Entry Title
    $entry['title'] = Format::stripTags($entry['title']);
    $entry['title'] = makeSafeEntities($entry['title']);
    $entry['title'] = htmlspecialchars(
        html_entity_decode($entry['title'], ENT_QUOTES, 'UTF-8'),
        ENT_QUOTES,
        'UTF-8'
    );
    $entry['title'] = correctSGML($entry['title']);
    $entry['title'] = str_replace($search, $replace, $entry['title']);

    // Strip Snippets
    $entry['body'] = preg_replace('!(#([a-zA-Z0-9_-]+)#)!', '', $entry['body']);

    // Format Entry Body
    $entry['body'] = Format::stripTags($entry['body']);
    $entry['body'] = makeSafeEntities($entry['body']);
    $entry['body'] = htmlspecialchars(
        html_entity_decode($entry['body'], ENT_QUOTES, 'UTF-8'),
        ENT_QUOTES,
        'UTF-8'
    );
    $entry['body'] = correctSGML($entry['body']);
    $entry['body'] = str_replace($search, $replace, $entry['body']);

?>
<item>
    <guid><?=sprintf(URL_BLOG_ENTRY, $entry['link']); ?></guid>
    <link><?=sprintf(URL_BLOG_ENTRY, $entry['link']); ?></link>
    <?php if (!empty($author)) { ?>
    <author><?=makeSafeEntities($author['email'] . ' (' . $author['first_name'] . ' ' . $author['last_name'] . ')'); ?></author>
    <?php } ?>
    <title><?=$entry['title']; ?></title>
    <description> <![CDATA[ <?=$entry['body']; ?> ]]> </description>
    <pubDate><?=date('D, d M Y H:i:s O', strtotime($entry['timestamp_published'])); ?></pubDate>
</item>
<?php } ?>
    </channel>
</rss>