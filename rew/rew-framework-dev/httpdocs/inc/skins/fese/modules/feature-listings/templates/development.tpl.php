<a class="col stk w1/2 w1/1-sm"<?=(!empty($result['url']) ? ' href="' . $result['url'] . '"' : ''); ?>>
    <div>
        <div class="img wFill h4/3 fade img--cover">
            <?php if (!empty($result['image'])) { ?>
                <img data-src="<?=$result['image']; ?>" alt="">
            <?php } else { ?>
                <img data-src="/img/404.gif" alt="">
            <?php } ?>
        </div>
    </div>
    <div>
        <?php if (!empty($result['unit_min_price']) && !empty($result['unit_max_price'])) { ?>
            <span class="nd-price">
                $<?=Format::shortNumber($result['unit_min_price']); ?>
                - <?=Format::shortNumber($result['unit_max_price']); ?>
            </span>
        <?php } else if (!empty($result['unit_min_price'])) { ?>
            <span class="nd-price">
                $<?=Format::number($result['unit_min_price']); ?>+
            </span>
        <?php } else if (!empty($result['unit_max_price'])) { ?>
            <span class="nd-price">
                $<?=Format::number($result['unit_max_price']); ?>
            </span>
        <?php } ?>
        <div class="nd-txt BM">
            <span class="nd-tag"><span>FEATURED DEVELOPMENT</span></span>
            <span class="title">
                <?=Format::htmlspecialchars($result['title']); ?>
                <?php if (!empty($result['city'])) { ?>
                    - <span class="nd-nbh"><?=Format::htmlspecialchars($result['city']); ?></span>
                <?php } ?>
            </span>
            <p>
                <?=Format::htmlspecialchars($result['subtitle']); ?>
                <br><?=!empty($result['tags']) ? Format::htmlspecialchars(' #' . implode(' #', $result['tags'])) : ''; ?>
            </p>
        </div>
    </div>
</a>