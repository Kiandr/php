<div class="bar">
    <div class="bar__title"><?= __('Reporting Tools'); ?></div>
</div>

<div class="block">

    <?php if (!empty($analytics)) { ?>
    <div class="field">
        <h3><?= __('Google Analytics'); ?></h3>
        <p><?= __('Connect to your Google Analytics account to access your website\'s report data.'); ?></p>
        <a href="analytics/" class="btn btn--positive"><?= __('Access Google Analytics'); ?></a>
    </div>
    <?php } ?>
    <?php if (!empty($response)) { ?>
    <div class="field">
        <h3><?= __('Agent Response Report'); ?></h3>
        <?php if ($reportsAuth->canViewResponseReport($authuser)) { ?>
        <p><?= __('View a breakdown of each agent\'s assigned leads, response rate and response times.'); ?></p>
        <?php } else { ?>
        <p><?= __('View a breakdown of your assigned leads, response rate and response times.'); ?></p>
        <?php } ?>
        <a href="agents/" class="btn btn--positive"><?= __('Access Response Report'); ?></a>
    </div>
    <?php } ?>
    <?php if (!empty($listing)) { ?>
    <div class="field">
        <h3><?= __('MLS&reg; Listing Report'); ?></h3>
        <p><?= __('Generate an MLS&reg; listing report to visualize lead activity for a particular MLS&reg; listing.'); ?></p>
        <a href="listing/" class="btn btn--positive"><?= __('Generate Listing Report'); ?></a>
    </div>
    <?php } ?>
    <?php if (!empty($dialer)) { ?>
    <div class="field">
        <h3><?= __('REW Dialer Report'); ?></h3>
        <p><?= __('View a summary of each agent\'s dialer session history.'); ?></p>
        <a href="dialer/" class="btn btn--positive"><?= __('Access REW Dialer Report'); ?></a>
    </div>
    <?php } ?>

    <?php if(!empty($tasks)) { ?>
    <div class="field">
        <h3><?= __('Task Report'); ?></h3>
        <p><?= __('View a report of the status of your agents\' action plan tasks'); ?></p>
        <a href="action_plans/" class="btn btn--positive"><?= __('Access Task Report'); ?></a>
    </div>
    <?php } ?>

</div>