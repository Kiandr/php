<div class="bar">
    <div class="bar__title"><?= __('Tools'); ?></div>
</div>

<div class="block">
    <?php
        echo $this->view->render('::partials/subdomain/selector', [
            'subdomain' => $subdomain,
            'subdomains' => $subdomains,
        ]);
    ?>
</div>

<div class="nodes">
    <ul class="nodes__list">
        <?php if ($show_communities): ?>
            <li class="nodes__branch">
                <div class="nodes__wrap">
                    <div class="article">
                        <div class="article__body">
                            <div class="article__thumb thumb thumb--medium -bg-rew2"><svg class="icon icon--invert"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-tools"></use></svg></div>
                            <div class="article__content">
                                <a class="text text--strong" href="/backend/cms/tools/communities/<?=$subdomainPostLink; ?>"><?= __('Communities'); ?></a>
                                <div class="text text--mute"><?= __('Create communities pages'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        <?php endif;?>
        <?php if ($show_conversion_tracking): ?>
            <li class="nodes__branch">
                <div class="nodes__wrap">
                    <div class="article">
                        <div class="article__body">
                            <div class="article__thumb thumb thumb--medium -bg-rew2"><svg class="icon icon--invert"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-tools"></use></svg></div>
                            <div class="article__content">
                                <a class="text text--strong" href="/backend/cms/tools/conversion-tracking/<?=$subdomainPostLink; ?>"><?= __('Conversion Tracking'); ?></a>
                                <div class="text text--mute"><?= __('Track conversions'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        <?php endif;?>
        <?php if ($show_rewrite): ?>
            <li class="nodes__branch">
                <div class="nodes__wrap">
                    <div class="article">
                        <div class="article__body">
                            <div class="article__thumb thumb thumb--medium -bg-rew2"><svg class="icon icon--invert"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-tools"></use></svg></div>
                            <div class="article__content">
                                <a class="text text--strong" href="/backend/cms/tools/rewrites/<?=$subdomainPostLink; ?>"><?= __('Redirect Rules'); ?></a>
                                <div class="text text--mute"><?= __('Redirect old URLs'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        <?php endif;?>
        <?php if ($show_slideshow): ?>
            <li class="nodes__branch">
                <div class="nodes__wrap">
                    <div class="article">
                        <div class="article__body">
                            <div class="article__thumb thumb thumb--medium -bg-rew2"><svg class="icon icon--invert"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-tools"></use></svg></div>
                            <div class="article__content">
                                <a class="text text--strong" href="/backend/cms/tools/slideshow/<?=$subdomainPostLink; ?>"><?= __('Slideshow'); ?></a>
                                <div class="text text--mute"><?= __('Manage your Slideshow'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        <?php endif;?>
        <?php if ($show_testimonials): ?>
            <li class="nodes__branch">
                <div class="nodes__wrap">
                    <div class="article">
                        <div class="article__body">
                            <div class="article__thumb thumb thumb--medium -bg-rew2"><svg class="icon icon--invert"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-tools"></use></svg></div>
                            <div class="article__content">
                                <a class="text text--strong" href="/backend/cms/tools/testimonials/<?=$subdomainPostLink; ?>"><?= __('Testimonials'); ?></a>
                                <div class="text text--mute"><?= __('Manage clients testimonials'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        <?php endif;?>
        <?php if ($show_radio_landing_page): ?>
            <li class="nodes__branch">
                <div class="nodes__wrap">
                    <div class="article">
                        <div class="article__body">
                            <div class="article__thumb thumb thumb--medium -bg-rew2"><svg class="icon icon--invert"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-tools"></use></svg></div>
                            <div class="article__content">
                                <a class="text text--strong" href="/backend/cms/tools/radio-landing-page/<?=$subdomainPostLink; ?>"><?= __('Radio Landing Page'); ?></a>
                                <div class="text text--mute"><?= __('Create a landing page for radio ads'); ?></div>
                            </div>

                        </div>
                    </div>
                </div>
            </li>
        <?php endif;?>
        <?php if ($show_backup): ?>
            <li class="nodes__branch">
                <div class="nodes__wrap">
                    <div class="article">
                        <div class="article__body">
                            <div class="article__thumb thumb thumb--medium -bg-rew2"><svg class="icon icon--invert"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-tools"></use></svg></div>
                            <div class="article__content">
                                <a class="text text--strong" href="/backend/cms/tools/backup/<?=$subdomainPostLink; ?>"><?= __('Backup Data'); ?></a>
                                <div class="text text--mute"><?= __('Backup CMS Data'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="nodes__branch">
                <div class="nodes__wrap">
                    <div class="article">
                        <div class="article__body">
                            <div class="article__thumb thumb thumb--medium -bg-rew2"><svg class="icon icon--invert"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-tools"></use></svg></div>
                            <div class="article__content">
                                <a class="text text--strong" href="/backend/cms/tools/backup/?files<?=str_replace("?", "&", $subdomainPostLink); ?>"><?= __('Backup Uploads'); ?></a>
                                <div class="text text--mute"><?= __('Backup CMS Uploads'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        <?php endif;?>
    </ul>
</div>
