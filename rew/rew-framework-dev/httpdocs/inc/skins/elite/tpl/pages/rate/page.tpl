<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<?php if ($this->container('feature')->countModules() > 0) { ?>
    <div id="feature" style="
        <?=($background = $this->variable('feature.background')) ? 'background-image: url(' . $background . ') !important;' : ''; ?>
        <?=($position = $this->variable('feature.position')) ? 'background-position: ' . $position . ' !important;' : ''; ?>
    ">
        <div class="module">
            
            <?php if ($this->container('slideshow')->countModules() > 0) { ?>
                <div id="slideshow">
                    <?php $this->container('slideshow')->loadModules(); ?>
                </div>
            <?php } ?>
            
            <div class="wrap">
                <?=$this->container('feature')->loadModules(); ?>
            </div>
        </div>
    </div>
<?php } ?>

<div id="sub-feature">
        <?php if ($this->container('mid-feature')->countModules() > 0) { ?>
            <?php $this->container('mid-feature')->loadModules(); ?>
        <?php } ?>
    <?php if ($this->container('sub-feature')->countModules() > 0) { ?>
        <?=$this->container('sub-feature')->loadModules(); ?>
    <?php } ?>
</div>

<?php

// Only show content if not empty
$content = $this->container('content')->loadModules(false);
if (!empty($content)) { ?>
    <section class="homepage-content">
        <div class="wrap">
            <?php echo $content; ?>
            <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
        </div>
    </section>
<?php } ?>

<?php if ($this->variable('showCtas') === true) { ?>

    <?php if (($snippet = rew_snippet('cta-about', false)) && $snippet !== 'cta-about') { ?>
        <section>
            <div class="wrap">
                <div class="l-align section-photo">
                    <img class="hidden-tablet" data-src="<?=$this->getUrl(); ?>/img/couple.png" alt="" style="margin-left: -230px;" />
                </div>
                <div class="r-align section-text">
                    <?=$snippet; ?>
                </div>
            </div>
        </section>
    <?php } ?>

    <?php if (($snippet = rew_snippet('cta-contact', false)) && $snippet !== 'cta-contact') { ?>
        <section class="dark padded">
            <div class="wrap">
                <div class="central">
                    <?=$snippet; ?>
                </div>
            </div>
        </section>
    <?php } ?>

    <?php if (!empty(Settings::getInstance()->MODULES['REW_PROPERTY_VALUATION'])) { ?>
        <?php if (($snippet = rew_snippet('cta-cma', false)) && $snippet !== 'cta-cma') { ?>
            <section>
                <div class="wrap">
                    <div class="l-align section-photo">
                        <img class="hidden-tablet" data-src="<?=$this->getUrl(); ?>/img/ipad-hand.png" style="margin-left: -60%;" alt="">
                    </div>
                    <div class="r-align section-text">
                        <?=$snippet; ?>
                    </div>
                </div>
            </section>
        <?php } ?>
    <?php } ?>

    <section class="light logo-section">
        <div class="wrap">
            <img data-src="<?=$this->getUrl(); ?>/img/logos.jpg" alt="">
        </div>
    </section>

    <?php if (($snippet = rew_snippet('cta-address', false)) && $snippet !== 'cta-address') { ?>
        <section class="padded">
            <div class="wrap">
                <div class="central">
                    <?=$snippet; ?>
                </div>
            </div>
        </section>
    <?php } ?>

<?php } ?>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>
