<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>
<?php $content = $this->container('content')->loadModules(false); ?>
<?php $community = $this->container('community')->loadModules(false); ?>

<?php if (!empty($community)) { ?>
    <?= $community; ?>
<?php }?>

<?php if ($content || $map) { ?>
    <div class="fw fw-nbh-about-cont">
        <div class="uk-container uk-container-center">
            <div class="uk-grid nbh-cont-grid">
                <?php if ($content) { ?>
                <div class="uk-width-1-1 <?=$map ? ' uk-width-large-1-2 nbh-cont-col' : ''?>">
                    <h5><?=$this->page->info('title'); ?></h5>
                    <?= $content; ?>
                </div>
                <?php } ?>
            </div><!-- /.uk-grid -->
        </div><!-- /.uk-container -->
    </div><!-- /.fw /.fw-nbh-about-cont -->
<?php }?>

<?php if (!empty(Settings::getInstance()->MODULES['REW_PROPERTY_VALUATION'])) { ?>
    <?php if ($this->container('home-valuation')->countModules() > 0) { ?>
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

<?php $this->includeFile('tpl/misc/blog-latest.tpl.php'); ?>

<div class="uk-container uk-container-center">
    <div class="mls-disclaimer">
        <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
    </div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>
