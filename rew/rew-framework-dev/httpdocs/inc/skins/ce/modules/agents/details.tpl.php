<?php if (empty($agent)) { ?>

    <div class="notice notice--negative">
        <p>We're sorry, but the agent you were looking for could not be found.</p>
    </div>

<?php } else { ?>

<div class="agent-details">
    <div class="columns -pad-bottom-lg">

        <div class="column -width-1/4 -width-1/2@md -width-1/1@sm -width-1/1@xs">
            <div class="hero hero--portrait">
                <div class="hero__fg">
                    <?php if(!empty($agent['title'])) { ?>
                    <div class="hero__head">
                        <div class="divider">
                            <span class="divider__label -left -text-upper -text-xs"><?=htmlspecialchars($agent['title']) ;?></span>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="hero__body -flex">
                        <div class="-bottom">
                            <h2 class="-text-upper"><?=htmlspecialchars($agent['name']); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="hero__bg">
                    <div class="cloak cloak--dusk"></div>
                    <img class="hero__bg-content" data-src="<?=$agent['image']; ?>" src="<?=$placeholder; ?>" alt="Photo of <?=htmlspecialchars($agent['name']); ?>">
                </div>
            </div>
        </div>


        <div class="column -width-3/4 -width-1/2@md -width-1/1@sm -width-1/1@xs -pad-horizontal@lg -pad-horizontal@xl">

            <h1><?=htmlspecialchars($agent['name']); ?></h1>

            <?php if (!empty($agent['testimonials'])) { ?>
                <?php foreach ($agent['testimonials'] as $testimonial) { ?>
                    <div class="agent-details-testimonial -mar-bottom">
                        <p><?=$testimonial['testimonial']; ?></p>
                        <?php if (!empty($testimonial['client'])) { ?>
                            <span><?=Format::stripTags($testimonial['client']); ?></span>
                        <?php } ?>
                        <div class="divider -mar-top">
                            <a href="/testimonials.php" class="divider__label -right">See All&hellip;</a>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>

            <?php if (!empty($agent['remarks'])) { ?>
                <div class="-mar-bottom">
                    <p class="description">
                        <?=$agent['remarks']; ?>
                    </p>
                </div>
            <?php } ?>

            <div class="divider -pad-vertical"><span class="divider__label -left -text-upper -text-xs">Contact <?=htmlspecialchars($agent['first_name']); ?></span></div>

            <div class="keyvals">
                <?php if (!empty($agent['website'])) { ?>
                <div class="keyvals__body">
                    <div class="keyval">
                        <span class="keyval__key">Website</span>
                        <span class="keyval__val"><a href="<?=htmlspecialchars($agent['website']); ?>" target="_blank"><?=str_replace('http://','', htmlspecialchars($agent['website'])); ?></a></span>
                    </div>
                </div>
                <?php } ?>
                <?php if (!empty($agent['email'])) { ?>
                <div class="keyvals__body">
                    <div class="keyval">
                        <span class="keyval__key">Email</span>
                        <span class="keyval__val"><a href="mailto:<?=$agent['email']; ?>"><?=$agent['email']; ?></a></span>
                    </div>
                </div>
                <?php } ?>
                <?php if (!empty($agent['office_phone'])) { ?>
                <div class="keyvals__body">
                    <div class="keyval">
                        <span class="keyval__key">Office Phone</span>
                        <span class="keyval__val"><?=htmlspecialchars($agent['office_phone']); ?></span>
                    </div>
                </div>
                <?php } ?>
                <?php if (!empty($agent['cell_phone'])) { ?>
                <div class="keyvals__body">
                    <div class="keyval">
                        <span class="keyval__key">Cellphone</span>
                        <span class="keyval__val"><?=htmlspecialchars($agent['cell_phone']); ?></span>
                    </div>
                </div>
                <?php } ?>
                <?php if (!empty($agent['home_phone'])) { ?>
                <div class="keyvals__body">
                    <div class="keyval">
                        <span class="keyval__key">Home Phone</span>
                        <span class="keyval__val"><?=htmlspecialchars($agent['home_phone']); ?></span>
                    </div>
                </div>
                <?php } ?>
                <?php if (!empty($agent['fax'])) { ?>
                <div class="keyvals__body">
                    <div class="keyval">
                        <span class="keyval__key">Fax</span>
                        <span class="keyval__val"><?=htmlspecialchars($agent['fax']); ?></span>
                    </div>
                </div>
                <?php } ?>
            </div>

            <?php if (!empty($agent['office'])) { ?>
                <a href="/offices.php?oid=<?=$office['id']; ?>" class="-pad-horizontal-lg">
                    <div class="divider -pad-vertical"></div>
                    <h2 class="-text-upper -text-bold -mar-bottom-0"><?=$office['title']; ?></h2>
                    <p class="-mar-vertical-0"><?=$office['location']; ?></p>
                    <?php if ($office['description']) { ?>
                        <p class="-mar-top-sm -text-xs -text-upper"><?=$office['description']; ?></p>
                    <?php } ?>
                </a>
            <?php } ?>

        </div>
    </div>
    <?php if (!empty($listings)) { ?>
        <div class="agents-listings -mar-top">
            <div class="divider">
                <span class="divider__label -left -text-upper -text-xs"><?=htmlspecialchars($agent['first_name']); ?>'s Listings</span>
                <?php /*<a class="divider__label -right -text-upper -text-xs">See All&hellip;</a>*/ ?>
            </div>
            <div class=" -mar-top">
                <?=$listings; ?>
            </div>
        </div>
    <?php } ?>
</div>
<?php } ?>