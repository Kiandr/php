<?php if ($this->container('blog-latest')->countModules() > 0 && ($posts = $this->container('blog-latest')->loadModules(false))) { ?>
    <section class="fw fw-home-blog">
        <div class="uk-container uk-container-center">
            <div class="uk-grid">
                <div class="uk-width-1-1 uk-width-medium-1-2 uk-width-xlarge-1-4 post-container">
                    <div class="home-post post-title">
                        <h5><?=$this->getPage()->variable('showBlogLatest.title'); ?></h5>
                        <a href="<?= Format::htmlspecialchars(Settings::getInstance()->URLS['URL_BLOG']); ?>">View All Posts <i class="uk-icon-angle-right"></i></a>
                    </div>
                </div>
                <?= $posts; ?>
            </div>
        </div>
    </section>
<?php } ?>
