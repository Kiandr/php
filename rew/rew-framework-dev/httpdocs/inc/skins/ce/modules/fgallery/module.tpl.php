<?php

// No gallery photos
if (empty($numImages)) {
    return;
}

// View gallery link
$galleryLink = sprintf(
    '<a class="action-gallery"%s>View Gallery</a>',
    (!empty($this->config['register']) ? sprintf( 'data-register="%s"', $this->config['register']) : '')
);

?>
<div id="<?=$this->getUID(); ?>" data-photos='<?=json_encode($gallery); ?>'>

    <?php if (empty($this->config['hidePhotos'])) { ?>
        <?php if ($numImages > 2) { ?>
        <div class="photoGrid3A">
            <div class="photoGrid3A--a"><img alt="" data-src="<?=$images[0]; ?>"></div>
            <div class="photoGrid3A--b"><img alt="" data-src="<?=$images[1]; ?>"></div>
            <div class="photoGrid3A--c"><img alt="" data-src="<?=$images[2]; ?>"><?=$galleryLink; ?></div>
        </div>
        <?php } elseif($numImages > 1) {?>
        <div class="photoGrid2A">
            <div class="photoGrid2A--a"><img alt="" data-src="<?=$images[0];?>"></div>
            <div class="photoGrid2A--b"><img alt="" data-src="<?=$images[1];?>"><?=$galleryLink; ?></div>
        </div>
        <?php } elseif (!empty($numImages)) { ?>
        <div class="photoGrid1A">
            <div class="photoGrid1A--a"><img alt="" data-src="<?=$images[0]; ?>"></div>
        </div>
        <?php } ?>
    <?php } ?>

    <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="pswp__bg"></div>
        <div class="pswp__scroll-wrap">
            <div class="pswp__container">
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
            </div>
            <div class="pswp__ui pswp__ui--hidden">
                <div class="pswp__top-bar">
                    <div class="pswp__counter"></div>
                    <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                    <button class="pswp__button pswp__button--share" title="Share"></button>
                    <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                    <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                    <div class="pswp__preloader">
                        <div class="pswp__preloader__icn">
                          <div class="pswp__preloader__cut">
                            <div class="pswp__preloader__donut"></div>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                    <div class="pswp__share-tooltip"></div>
                </div>
                <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
                <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
                <div class="pswp__caption">
                    <div class="pswp__caption__center"></div>
                </div>
            </div>
        </div>
    </div>
</div>