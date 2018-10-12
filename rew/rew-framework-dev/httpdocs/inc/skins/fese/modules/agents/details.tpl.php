<?php if (empty($agent)) { ?>

    <div class="msg negative">
        <p>We're sorry, but the agent you were looking for could not be found.</p>
    </div>

<?php } else { ?>

    <h1><?=Format::htmlspecialchars($agent['name']); ?></h1>
    <h2><?=Format::htmlspecialchars($agent['title']) ;?></h2>

    <div class="cols">

        <div class="col agent-info-col w3/8 w1/1-sm">

            <div class="photo">
                <img data-src="<?=$agent['image']; ?>" src="<?=$placeholder; ?>" alt="">
            </div>

            <ul class="kvs">
                <?php if (!empty($agent['office'])) { ?>
                    <?php $office = $agent['office']; ?>
                    <li class="kv office">
                        <strong class="k">Office</strong>
                        <span class="v">
                            <a href="/offices.php?oid=<?=$office['id']; ?>"><?=Format::htmlspecialchars($office['title']); ?></a>
                            <?php if (!empty($office['location'])) { ?>
                                <br /><?=Format::htmlspecialchars($office['location']); ?>
                            <?php } ?>
                        </span>
                    </li>
                <?php } ?>
                <?php if (!empty($agent['email'])) { ?>
                    <li class="kv email">
                        <strong class="k">Email</strong>
                        <span class="v">
                            <a href="mailto:<?=$agent['email']; ?>"><?=$agent['email']; ?></a>
                        </span>
                    </li>
                <?php } ?>
                    <?php if (!empty($agent['office_phone'])) { ?>
                        <li class="kv officephone">
                            <strong class="k">Office #</strong>
                            <span class="v"><?=Format::htmlspecialchars($agent['office_phone']); ?></span>
                        </li>
                    <?php } ?>
                <?php if (!empty($agent['cell_phone'])) { ?>
                    <li class="kv cellphone">
                        <strong class="k">Cell #</strong>
                        <span class="v"><?=Format::htmlspecialchars($agent['cell_phone']); ?></span>
                    </li>
                <?php } ?>
                <?php if (!empty($agent['home_phone'])) { ?>
                    <li class="kv homephone">
                        <strong class="k">Home #</strong>
                        <span class="v"><?=Format::htmlspecialchars($agent['home_phone']); ?></span>
                    </li>
                <?php } ?>
                <?php if (!empty($agent['fax'])) { ?>
                    <li class="kv fax">
                        <strong class="k">Fax #</strong>
                        <span class="v"><?=Format::htmlspecialchars($agent['fax']); ?></span>
                    </li>
                <?php } ?>
                <?php if (!empty($agent['website'])) { ?>
                    <li class="kv website">
                        <strong class="k">Website</strong>
                        <span class="v">
                            <a href="<?=Format::htmlspecialchars($agent['website']); ?>" target="_blank"><?=Format::htmlspecialchars($agent['website']); ?></a>
                        </span>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <div class="col agent-bio-col w5/8 w1/1-sm">
            <?php if (!empty($agent['remarks'])) { ?>
                <p class="description"><?=$agent['remarks']; ?></p>
            <?php } ?>
        </div>

    </div>
    <?php

    // Agent's Listings
    if (!empty($listings)) {
        echo '<div class="agents-listings">';
        echo '<h2>' . Format::htmlspecialchars($agent['name']) . '\'s ' . Lang::write('MLS') . ' Listings</h2>';
        echo $listings;
        echo '</div>';
    }

}

?>
<div class="marV-md">
    <a class="btn btn--primary" href="/agents.php">View All Agents</a>
</div>