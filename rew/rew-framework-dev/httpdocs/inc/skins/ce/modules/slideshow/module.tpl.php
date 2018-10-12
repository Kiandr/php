<div>
    <div id="<?=$this->getUID(); ?>">
        <?php foreach ($slideshow as $i => $image) {
            $class = '';
            if ($i == 0) {
                $class = 'active';
            } else if($i ==  1) {
                $class = 'next';
            } else if ($i == count($slideshow) - 1) {
                $class = 'prev';
            }
            $style = sprintf(
                "background-image: url('%s'); filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='%s', sizingMethod='scale');",
                $image['image'],
                $image['image']
            );
        ?>
        <div class="slide <?=$class; ?>" data-background="<?=$image['image']; ?>" style="<?= (!empty($class)) ? $style : ''; ?>"></div>
        <?php } ?>
        <?php if (!empty($vr)) { ?>
        <div id="360photo<?=$this->getUID(); ?>" data-vr-src="<?=$vr; ?>" class="slide actual active 360" tabindex="0"></div>
        </div>
        <?php } ?>
    </div>
    <div class="slider-controls">
        <button class="button button--ghost -left -pad-sm">
            <svg class="prev" viewBox="0 0 9.9 16.8" width="60" height="60" role="img" aria-labelledby="title">
                <title>Previous</title>
                <desc>View the Previous slide</desc>
                <path d="M309.14,324.53a1.5,1.5,0,0,1-1.06-2.56l5.84-5.84-5.84-5.84a1.5,1.5,0,0,1,2.12-2.12l6.9,6.9a1.5,1.5,0,0,1,0,2.12l-6.9,6.9A1.5,1.5,0,0,1,309.14,324.53Z" transform="translate(-307.64 -307.72)"></path>
            </svg>
        </button>
        <button class="button button--ghost -right -pad-sm">
            <svg class="next" viewBox="0 0 9.9 16.8" width="60" height="60" role="img" aria-labelledby="title">
                <title>Next</title>
                <desc>View the next slide</desc>
                <path d="M309.14,324.53a1.5,1.5,0,0,1-1.06-2.56l5.84-5.84-5.84-5.84a1.5,1.5,0,0,1,2.12-2.12l6.9,6.9a1.5,1.5,0,0,1,0,2.12l-6.9,6.9A1.5,1.5,0,0,1,309.14,324.53Z" transform="translate(-307.64 -307.72)"></path>
            </svg>
        </button>
    </div>
</div>