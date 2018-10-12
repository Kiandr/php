<?php if (!empty($testimonials)) { ?>
    <div<?=!empty($class) ? ' class="' . $class . '"' : ''; ?>>

        <?php if (!empty($title)) { ?>
            <h4><?=$title; ?></h4>
        <?php } ?>

        <?php foreach ($testimonials as $testimonial) { ?>
            <blockquote>
                <p><?=$testimonial['testimonial']; ?></p>
                <?=!empty($testimonial['client']) ? '<small>' . $testimonial['client'] . '</small>' : ''; ?>
            </blockquote>
        <?php } ?>

        <?php if (!empty($link)) { ?>
            <a class="btn" href="/testimonials.php"><?=$link; ?></a>
        <?php } ?>

    </div>
<?php } ?>
