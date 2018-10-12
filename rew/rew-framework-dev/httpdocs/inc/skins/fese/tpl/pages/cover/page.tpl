<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<?php if ($this->container('feature')->countModules() > 0) { ?>
    <div id="feature" class="cover">
        <div class="module">
            <?php $this->container('feature')->loadModules(); ?>
        </div>
    </div>
<?php } ?>

<div class="home-developments cols">
    <?php if ($this->container('featured')->countModules() > 0) { ?>
        <?php $this->container('featured')->loadModules(); ?>
    <?php } ?>
</div>

<div id="body">
    <div id="content">
        <div class="wrp S4 h-feat-homes">
            <?php if ($this->container('featured-listings')->countModules() > 0) { ?>
                <div class="section h-feat-listings">
                    <?php if ($featuredListingsTitle = $this->getPage()->variable('showFeaturedListings.title')) { ?>
                        <h2><?=$featuredListingsTitle; ?></h2>
                    <?php } ?>
                    <?php $this->container('featured-listings')->loadModules(); ?>
                </div>
            <?php } ?>
            <?php if ($this->container('featured-agents')->countModules() > 0) { ?>
                <div class="section h-feat-agents">
                    <?php if ($featuredAgentsTitle = $this->getPage()->variable('showFeaturedAgents.title')) { ?>
                        <h2><?=$featuredAgentsTitle; ?></h2>
                    <?php } ?>
                    <div class="cols">
                        <?php $this->container('featured-agents')->loadModules(); ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="wrp S4">
            <?php $this->container('content')->loadModules(); ?>
            <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
        </div>
    </div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>