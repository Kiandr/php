<?php if (empty($office)) { ?>

    <div class="msg negative">
        <p>We're sorry, but the office you were looking for could not be found.</p>
    </div>

<?php } else { ?>

    <h1><?=Format::htmlspecialchars($office['title']); ?></h1>
    <?php if (!empty($office['location'])) { ?>
        <h2><?=Format::htmlspecialchars($office['location']); ?></h2>
    <?php } ?>

    <div class="cols">
        <div class="col w5/8">
            <?php if (!empty($office['description'])) { ?>
                <p class="description">
                    <?=$office['description']; ?>
                </p>
            <?php } ?>
        </div>
        <div class="col w3/8">
            <div class="photo">
                <img data-src="<?=$office['image']; ?>" src="<?=$office_placeholder; ?>" alt="">
            </div>
            <ul class="kvs">
                <?php if (!empty($office['phone'])) { ?>
                    <li class="kv phone">
                        <strong class="k">Phone #</strong>
                        <span class="v"><?=Format::htmlspecialchars($office['phone']); ?></span>
                    </li>
                <?php } ?>
                <?php if (!empty($office['fax'])) { ?>
                    <li class="kv fax">
                        <strong class="k">Fax #</strong>
                        <span class="v"><?=Format::htmlspecialchars($office['fax']); ?></span>
                    </li>
                <?php } ?>
                <?php if (!empty($office['email'])) { ?>
                    <li class="kv email">
                        <strong class="k">Email</strong>
                        <span class="v">
                            <a href="mailto:<?=$office['email']; ?>"><?=$office['email']; ?></a>
                        </span>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <?php if (!empty($office['agents'])) { ?>
        <div class="cols">
            <h2>Agents in this Office</h2>
            <?php foreach ($office['agents'] as $agent) { ?>
                <div class="col img img--cover w1/4 w1/2-md w1/1-sm h1/1">
                    <a href="/agents/<?=$agent['link']; ?>/">
                        <img data-src="<?=$agent['image']; ?>" src="<?=$agent_placeholder; ?>" alt="">
                    </a>
                    <div class="cpt"><?=Format::htmlspecialchars($agent['name']); ?></div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

<?php } ?>

<div class="marV-md">
    <a class="btn" href="/offices.php">View All Offices</a>
</div>