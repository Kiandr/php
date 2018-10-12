<?php 
if (!empty($posts)) {?>
    <?php foreach ($posts as $i => $post) { ?>
        <div class="uk-width-1-1 uk-width-medium-1-2 uk-width-xlarge-1-4 post-container">
            <div class="home-post">
                <span class="blog-date"><?= date('F j, Y', strtotime($post['timestamp_published'])); ?></span>
                <span class="blog-title"><?= Format::htmlspecialchars($post['title']); ?></span>
                <a href="<?= Format::htmlspecialchars(Settings::getInstance()->URLS['URL_BLOG'] . $post['link']); ?>.html">Read More <i class="uk-icon-angle-right"></i></a>
                <?php if (!empty($post['tags'])) { ?>
                    <span class="blog-tag"><?=Format::htmlspecialchars(strstr($post['tags'], ',', true) ?: $post['tags']); ?></span>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>
