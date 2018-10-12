<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<!-- HOMEPAGE SLIDESHOW -->
<?php
    $background = null;
    // Validate feature
    $feature = $this->variable('feature');
    if ($feature == 'slide_photo' && $this->container('slideshow')->countModules() == 0) $feature = '';
    else if ($feature == 'photo' && !($background = $this->variable('feature.background'))) $feature = '';
?>
<?php if (in_array($feature, array('photo', 'slide_photo'))) { ?>
    <section id="feature" class="homepage-slideshow<?= $feature == 'photo' ? '' : ' js-homepage-slideshow'; ?> uk-clearfix">
        <?php if ($this->variable('showCtas') === true && $snippet = rew_snippet('cta-feature', false)) { ?>
            <div class="slide-txt uk-position-z-index">
            <?= $snippet; ?>
          </div>
        <?php } ?>
        <div class="module">
            <div id="slideshow">
                <?php if ($feature == 'photo') { ?>
                    <div class="slide">
                        <img src="/thumbs/1903x/r<?= Format::htmlspecialchars($background); ?>" alt="" class="uk-cover-object">
                    </div>
                <?php } else { ?>
                    <?php $this->container('slideshow')->loadModules(); ?>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<!-- HOMEPAGE CTA -->
<?php if (!in_array($snippet = rew_snippet('ctas-homepage', false), array('ctas-homepage', ''))) { ?>
<section class="cta-containers">
    <div class="uk-container-xlarge uk-container-center">
        <div class="uk-grid uk-grid-small">
            <?= $snippet; ?>
        </div>
    </div>
</section>
<?php } ?>

<!-- HOMEPAGE CMS CONTENT -->
<section class="homepage-cms-content">
    <div class="uk-container-xlarge uk-container-center uk-margin-large">
        <?php $this->container('content')->loadModules(); ?>
        <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
    </div> 
</section>

<!--HOMEPAGE FEATURED COMMUNITIES -->
<?php if ($communities = $this->container('communities')->loadModules(false)) { ?>
<section class="fw fw-home-featured-comm uk-clearfix">
    <?= $communities; ?>
</section>
<?php } ?>

<!-- COMPANY INFO SECTION -->
<?php if (!in_array($companyInfo = rew_snippet('company-info', false), array('', 'company-info'))) { ?>
<section class="fw fw-comp-info">
    <div class="uk-container uk-container-center">
        <div class="uk-grid uk-grid-collapse">
            <div class="uk-width-1-1">
                <?= $companyInfo; ?>
        		</div>
        </div>
    </div>
</section>
<?php } ?>

<?php if (!empty(Settings::getInstance()->MODULES['REW_PROPERTY_VALUATION'])) { ?>
    <?php if ($this->container('home-valuation')->countModules() > 0) { ?>
        <!-- HOME VALUATION FORM -->
        <section class="uk-cover-background home-valuation-section uk-position-relative">
            <div class="uk-container uk-container-center">
                <div class="home-valuation-container">
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-small-1-1 uk-width-medium-1-1 uk-width-large-1-1 uk-width-xlarge-3-10 home-valuation-title">
                            <h5>Thinking of Selling?</h5>
                            <span>Get your free home valuation here.</span>
                        </div>
                        <?php $this->container('home-valuation')->loadModules(); ?>
                    </div>
                </div>
            </div>
        </section>
    <?php } ?>
<?php } ?>

<!-- HOMEPAGE FEATURED AGENTS -->
<?php if ($this->container('agents')->countModules() > 0 && ($agents = $this->container('agents')->loadModules(false))) { ?>
<section class="fw fw-featured-agents">
    <div class="uk-container uk-container-center">
        <div class="uk-grid uk-grid-medium uk-clearfix">
            <h3><?=$this->getPage()->variable('showAgents.title'); ?></h3>
            <?= $agents; ?>
        </div>
    </div>
</section>
<?php } ?>

<!-- HOMEPAGE TESTIMONIALS -->
<?php if ($this->container('testimonials')->countModules() > 0 && ($testimonials = $this->container('testimonials')->loadModules(false))) { ?>
<section class="fw fw-home-testimonial">
    <div class="uk-container uk-container-center h-testimonial-container">
        <div class="uk-flex">
            <div class="uk-width-1-1 uk-width-small-4-5 uk-width-medium-1-1 uk-width-large-4-6 uk-width-xlarge-2-4">
                <div class="h-testimonial-box">
                    <h5><?=$this->getPage()->variable('showTestimonials.title'); ?></h5>
                    <?= $testimonials; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php } ?>

<?php $this->includeFile('tpl/misc/blog-latest.tpl.php'); ?>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>
