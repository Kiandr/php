<?php foreach ($agents as $i => $agent) { ?>
    <div class="col img img--cover w1/6 w1/4-md w1/2-sm h1/1">
        <?=$agent['link'] ? sprintf('<a href="%s">', $agent['link']) : ''; ?>
            <img src="<?=$placeholder; ?>" data-src="<?= Format::htmlspecialchars($agent['image']); ?>" alt="">
            <div class="cpt"><?=Format::htmlspecialchars($agent['name']); ?></div>
        <?=$agent['link'] ? '</a>' : ''; ?>
    </div>
<?php } ?>