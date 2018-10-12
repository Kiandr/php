<?php if (!empty($agent)) { ?>
    <div class="agent-details">
        <div class="wrap">
            <div class="wrap-inner">
                <?php if (!empty($agent['image'])) { ?>
                    <img src="<?=$agent['image']; ?>" alt="">
                <?php } ?>
                <h3><?=Format::htmlspecialchars($agent['name']); ?></h3>
                <h4 class="small-caps"><?= Format::htmlspecialchars($agent['title']); ?></h4>
                <?php if (!empty($agent['office_phone']) || !empty($agent['cell_phone'])) { ?>
                    <ul>
                        <?php if (!empty($agent['office_phone'])) { ?>
                            <li>
                                <strong>Office</strong>
                                <span><?= Format::htmlspecialchars($agent['office_phone']); ?>
                            </li>
                        <?php } ?>
                        <?php if (!empty($agent['cell_phone'])) { ?>
                            <li>
                                <strong>Cell</strong>
                                <span><?= Format::htmlspecialchars($agent['cell_phone']); ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
                <a href="<?= Format::htmlspecialchars($listing['url_inquire']); ?>?agent=<?= $agent['id']; ?>" class="buttonstyle popup">Questions?</a>
            </div>
        </div>
    </div>
<?php } ?>
