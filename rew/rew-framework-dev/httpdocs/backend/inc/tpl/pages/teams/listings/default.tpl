<?php include('inc/tpl/app/menu-teams.tpl.php'); ?>
<div class="bar">
    <a class="bar__title" data-drop="#menu--filters" href="javascript:void(0);"><?= __('Team Listings'); ?> <svg class="icon icon-drop"><use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink"/></svg></a>
    <div class="bar__actions">
        <a class="bar__action timeline__back" href="<?=URL_BACKEND; ?>teams?back"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
    </div>
</div>
<?php include('inc/tpl/app/summary-team.tpl.php'); ?>

<?php if (!empty($listings)) { ?>
    <div class="nodes">
        <ul class="nodes__list">
            <?php foreach ($listings as $listing) { ?>
                <li class="nodes__branch">
                    <div class="article">
                        <div class="nodes__wrap">
                            <div class="article">
                                <div class="article__body">
                                    <div class="article__thumb thumb thumb--medium">
                                        <?php if (!empty($listing['image'])) { ?>
                                            <img src="<?=$listing['image']; ?>" alt="">
                                        <?php } else { ?>
                                            <img src="/thumbs/60x60/uploads/listings/na.png" alt="">
                                        <?php } ?>
                                    </div>
                                    <div class="article__content">
                                        <a href="<?=URL_BACKEND; ?>teams/listings/listing/?team=<?=$_GET['id']; ?>&id=<?=$listing['id']; ?>&feed=<?=$listing['feed']; ?>" class="text text--strong"><?=Format::htmlspecialchars($listing['title']); ?></a>
                                        <div class="text text--mute">$<?=Format::number($listing['price']); ?> - <?=Format::htmlspecialchars($listing['address']); ?>, <?=Format::htmlspecialchars($listing['city']); ?>, <?=Format::htmlspecialchars($listing['state']); ?></div>
                                    </div>
                                </div>
                                <div class="article__foot">
                                    <span class="token">
                                        <?php if (!empty($listing['agent_photo'])) { ?>
                                            <div class="token__thumb thumb thumb--tiny">
                                                <img src="/thumbs/60x60/uploads/agents/<?=urlencode($listing['agent_photo']); ?>" alt="">
                                            </div>
                                        <?php } else { ?>
                                            <div class="token__thumb thumb thumb--tiny -bg-<?=strtolower($listing['last_name'][0]); ?>">
                                                <span class="thumb__label"><?=$listing['first_name'][0] . $listing['last_name'][0]; ?></span>
                                            </div>
                                        <?php } ?>
                                        <a href="<?=URL_BACKEND; ?>agents/agent/summary/?id=<?=$listing['agent']; ?>" class="token__label"><?=Format::htmlspecialchars($listing['agent_name']); ?></a>
                                    </span>
                                    <?php foreach ($listing['featured_agents'] as $featured_agent) { ?>
                                        <span class="token">
                                            <?php if (!empty($featured_agent['image'])) { ?>
                                                <div class="token__thumb thumb thumb--tiny">
                                                    <img src="/thumbs/60x60/uploads/agents/<?=urlencode($featured_agent['image']); ?>">
                                                </div>
                                            <?php } else { ?>
                                                <div class="token__thumb thumb thumb--tiny -bg-<?=strtolower($featured_agent['last_name'][0]); ?>">
                                                    <span class="thumb__label"><?=$featured_agent['first_name'][0] . $featured_agent['last_name'][0]; ?></span>
                                                </div>
                                            <?php } ?>
                                            <a href="<?=URL_BACKEND; ?>agents/agent/summary/?id=<?=$featured_agent['id']; ?>" class="token__label"><?=Format::htmlspecialchars($featured_agent['name']); ?></a>
                                        </span>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="nodes__actions">
                                <?php if ($can_feature) { ?>
                                    <a class="btn btn--ico btn--ghost edit" href="listing/?team=<?=urlencode($_GET['id']); ?>&id=<?=$listing['id']; ?>&feed=<?=$listing['feed']; ?>">
                                        <svg class="icon icon-team mar0">
                                            <use xlink:href="/backend/img/icos.svg#icon-team"/>
                                        </svg>
                                    </a>
                                <?php } ?>
                                <a class="btn btn--ico btn--ghost view" href="<?=$listing['link']; ?>" target="_blank">
                                    <svg class="icon icon-view mar0">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-view"></use>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
    <?=$this->view->render('inc/tpl/partials/pagination.tpl.php', $pagination); ?>
<?php } else { ?>
    <div class="block">
        <p><?= __('No Listings Found'); ?></p>
    </div>
<?php } ?>