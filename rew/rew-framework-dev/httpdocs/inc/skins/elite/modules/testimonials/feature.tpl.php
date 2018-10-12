<?php if (!empty($testimonials)) { ?>
    <div class="h-testimonial-slider fw-slider" data-fw-slider-config='{"arrows": false, "dots": true}'>

        <?php foreach ($testimonials as $testimonial) {
                if ($host = parse_url($testimonial['link'], PHP_URL_HOST)) {
                    $host = substr($host, 0, strrpos($host,'.'));
                    $host = substr($host, strrpos($host,'.')+1);
                    if ($host == 'google') $host.='-plus';
                }
            ?>
            <div class="slide">
                <?php if (!empty($testimonial['client'])){?>
                    <div class="name">
                        <span><?=$testimonial['client']?></span>
                        <?php if ($host) {?>
                            <a href="<?=$testimonial['link']?>" target="_blank"><?=$host?> <i class="uk-icon-<?=$host?>"></i></a>
                        <?php } ?>
                    </div><!-- /.name -->
                <?php } ?>
                <div class="testimonial">
                    <div class="testimonial-p"><?=$testimonial['testimonial']; ?></div>

                    <br>

                    <div class="testimonials-links">
                        <a href="/testimonials.php" class="uk-button uk-button-primary">Read More</a>
                    </div>
                </div>
            </div>

        <?php } ?>
    </div>
<?php } ?>
