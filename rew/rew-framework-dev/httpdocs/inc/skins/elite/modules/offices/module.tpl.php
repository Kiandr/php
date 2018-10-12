<?php if (!empty($offices)) { ?>
    <div class="module articleset offices<?=(!empty($class) ? ' ' . $class : ''); ?>">
        <?php foreach ($offices as $office) { ?>
            <div class="uk-article">

                <header>
                    <h4><?= Format::htmlspecialchars($office['title']); ?></h4>
                </header>

                <div class="uk-grid">

                    <div class="uk-width-1-1 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-4">
                        <a href="<?= sprintf(Settings::getInstance()->URLS['URL_OFFICE'], $office['id']); ?>">
                            <img class="uk-width-1-1" data-src="<?= Format::htmlspecialchars($office['image']); ?>" src="<?= Format::htmlspecialchars($office_placeholder); ?>" alt="">
                        </a>
                    </div>

                    <div class="uk-width-1-1 uk-width-small-1-2 uk-width-medium-2-3 uk-width-large-3-4">

                        <p class="description"><?= Format::htmlspecialchars($office['description']); ?></p>

                        <ul class="uk-list">
                            <?php if (!empty($office['location'])) { ?>
                                <li class="keyval location"><strong>Address</strong> <span> <?= Format::htmlspecialchars($office['location']); ?></span></li>
                            <?php } ?>
                            <?php if (!empty($office['phone'])) { ?>
                                <li class="keyval phone"><strong>Phone #</strong> <span><?= Format::htmlspecialchars($office['phone']); ?></span></li>
                            <?php } ?>
                            <?php if (!empty($office['fax'])) { ?>
                                <li class="keyval fax"><strong>Fax #</strong> <span><?= Format::htmlspecialchars($office['fax']); ?></span></li>
                            <?php } ?>
                            <?php if (!empty($office['email'])) { ?>
                                <li class="keyval email"><strong>Email</strong> <span><a href="mailto:<?= $office['email']; ?>"><?= $office['email']; ?></a></span></li>
                            <?php } ?>
                        </ul>

                    </div>

                </div>

                <div class="uk-margin-top">
                    <a class="uk-button" href="/offices.php?oid=<?=$office['id']; ?>">Read More <i class="icon-chevron-right"></i></a>
                    <?php if (!empty($link)) { ?>
                        <a class="btn" href="/offices.php"><?=$link; ?> <i class="icon-chevron-right"></i></a>
                    <?php } ?>
                </div>
            </div>
            <hr>
        <?php } ?>
    </div>
<?php } ?>
