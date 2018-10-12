<div id="<?=$this->getUID(); ?>" class="fw-slider" data-fw-slider-config='{"deferred": true, "adaptiveHeight": false, "arrows": false, "autoplay": true}'>
    <?php foreach ($slideshow as $i => $image) { ?>
        <?php // Load first slide. Defer the rest. ?>
        <?php
            $styleConfig = array(
                ($i === 0 ? 'background-image' : 'backgroundImage') => 'url("' . Format::htmlspecialchars($image['image']) . '")',
                'filter' => 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' . Format::htmlspecialchars($image['image']) . '", sizingMethod="scale");'
            );

            if ($i === 0) {
                // Eager load first image
                $style = 'style="';
                foreach ($styleConfig as $key => $value) {
                    $style .= $key . ": " . Format::htmlspecialchars($value) . ";";
                }
                $style .= '"';
            } else {
                // Defer loading of subsequent images
                $styleConfig['display'] = 'block';
                $style = 'style="display: none" data-fw-deferred-img-config="' . Format::htmlspecialchars(json_encode(['src' => $image['image'], 'sizes' => $this->getPage()->getSkin()->getPhotoSizes($image['image']), 'style' => $styleConfig])) . '"';
            }
        ?>
        <?php if (!empty($image['link'])) { ?>
            <a class="slide" href="<?= Format::htmlspecialchars($image['link']); ?>" <?= $style; ?>></a>
        <?php } else {?>
            <div class="slide" <?= $style; ?>></div>
        <?php } ?>
    <?php } ?>
</div>
