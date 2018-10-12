<?php

// Category Breadcrumbs
$_GET['category'] = isset($_POST['listing_category'][0]) ? $_POST['listing_category'][0] : $_GET['category'];
if (!empty($_GET['category'])) {

    // DB Connection
    $db = DB::get('directory');

    // Build Breadcrumbs
    $breadcrumbs = array();
    $result = $db->prepare("SELECT `c`.`link` AS `c`, `s`.`link` AS `s`, `t`.`link` AS `t` FROM `directory_categories` `c` LEFT JOIN `directory_categories` `s` ON `c`.`parent` = `s`.`link` LEFT JOIN `directory_categories` `t` ON `s`.`parent` = `t`.`link` WHERE `c`.`link` = :category;");
    $result->execute(array('category' => $_GET['category']));
    $categories = $result->fetch();
    if (!empty($categories)) {
        $categories = array_reverse($categories);
        $breadcrumb = $db->prepare("SELECT `link`, `title` FROM `directory_categories` WHERE `link` = :category LIMIT 1;");
        foreach ($categories as $crumb) {
            if (empty($crumb)) continue;
            $breadcrumb->execute(array('category' => $crumb));
            $crumb = $breadcrumb->fetch();
            if (!empty($crumb)) {
                $breadcrumbs[] = array(
                    'title'		=> $crumb['title'],
                    'link'		=> URL_DIRECTORY . $crumb['link'] . '/',
                    'notlink'	=> ($_GET['page'] != 'details' && $_GET['category'] == $crumb['link'])
                );
            }
        }
    }

    // Listing Details
    if ($_GET['page'] == 'details' && !empty($entry)) {
        $breadcrumbs[] = array('link' => $entry['url_details'], 'title' => $entry['business_name'], 'notlink' => true);
    }

    // Directory Home
    if (!empty($breadcrumbs)) array_unshift($breadcrumbs, array('link' => URL_DIRECTORY, 'title' => 'Directory Home'));

}

?>
<div id="directory-header">
    <?php

        // Show Heading
        if ((empty($_GET['page']) || $_GET['page'] == 'directory') && !empty($directory_settings['directory_name'])) echo '<h1>' . $directory_settings['directory_name'] . '</h1>';

    ?>
    <form action="<?=URL_DIRECTORY_SEARCH; ?>" class="search">
        <h4>
            Find a Business
            <span class="tween">&bull;</span>
            <a href="<?=URL_DIRECTORY; ?>add/?listing_category[]=<?=Format::htmlspecialchars($_GET['category']); ?>">Add a Business</a>
        </h4>
        <div class="mmm">
            <div class="ac-input">
            <input type="search" placeholder='E.G. "Coffee", "Home and Garden".' name="search" value="<?=Format::htmlspecialchars($_GET['search']); ?>" required>
            </div>
            <button type="submit" class="search-btn btn btn--primary">
                <svg style="width:16px;height:16px;vertical-align: middle; position: relative; top: -1px; margin: -1px 0 0 0" viewBox="0 0 24 24">
                    <path fill="#444" d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"></path>
                </svg>
                Search
            </button>
        </div>
    </form>
    <?php rew_snippet('business-directory-intro'); ?>
</div>

<?php if (!empty($breadcrumbs)) { ?>
    <div class="breadcrumbs">
        <ul>
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php if ($breadcrumb['notlink']) { ?>
                    <li><?=Format::htmlspecialchars($breadcrumb['title']); ?></li>
                <?php } else { ?>
                    <li><a href="<?=$breadcrumb['link']; ?>"><?=Format::htmlspecialchars($breadcrumb['title']); ?></a></li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
<?php } ?>