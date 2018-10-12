<form action="<?=$form_action;?>" method="post" class="rew_check">

	<div class="bar">
		<div class="bar__title"><?= __('Follow Up Boss'); ?></div>
		<div class="bar__actions">
    		<?php if (isset($_GET['setup'])) { ?>
    		<a class="bar__action" href="/backend/settings/partners/followupboss/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
    		<?php } else { ?>
    		<a class="bar__action" href="/backend/settings/partners/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
            <?php } ?>
        </div>
	</div>

    <div class="block">

    	<div class="btns btns--stickyB">
        	<span class="R">

        		<?php if (isset($_GET['setup'])) { ?>

                    <?php if (!empty($logins_valid)) { ?>
        		    <a class="btn delete" href="../?disconnect=followupboss" onclick="javascript:return confirm('<?= __('Are you sure you want to disable the integration with this partner?'); ?>');"><?= __('Disable Integration'); ?></a>
                    <?php } ?>

        		    <button class="btn btn--positive" type="submit"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>

        		<?php } else if (empty($logins_valid)) { ?>

        		<?php } else { ?>
        		    <a href="?setup" class="btn settings"><?= __('Integration Settings'); ?></a>
                    <button class="btn btn--positive" type="submit"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>
        		<?php } ?>

    		</span>
        </div>



	<?php if (isset($_GET['setup'])) { ?>

    	<div class="field">
    	    <label class="field__label"><?= __('API Key'); ?></label>
            <input class="w1/1" type="text" name="api_key" value="<?=htmlspecialchars($_POST['api_key']); ?>">
            <label class="hint"><?= __('You can find this in the Follow Up Boss account dashboard under My Settings &raquo; Send in Leads &raquo; Follow Up Boss API Key'); ?></label>
        </div>

	<?php } else if (empty($logins_valid)) { ?>

        <h2><?= __('Integration Status'); ?></h2>
        <p><?= __(
                'Follow Up Boss integration is currently %s . To use this feature you must %s.',
                '<strong>' . __('inactive') . '</strong>',
                '<a href="?setup">' . __('provide your Follow Up Boss API Key') . '</a>'); ?>
        </p>

    <?php } else { ?>

    	<h2><?= __('Integration Status'); ?></h2>
    	<p> <?= __('Follow Up Boss integration is currently %s. Your IDX will automatically notify Follow Up Boss whenever any of the following actions occur: ', '<strong>' . __('configured and active') . '</strong>'); ?></p>
    	<ul>
    		<li><?= __('New Lead Registrations'); ?></li>
    		<li><?= __('Property Inquiries'); ?></li>
    		<li><?= __('Contact Form Submissions'); ?></li>
    		<li><?= __('Listing Details Views'); ?></li>
    		<li><?= __('Listings Added to %s', Locale::spell(__('Favorites'))); ?>
    		</li>
    		<li><?= __('Searches Performed'); ?></li>
    		<li><?= __('Searches Saved'); ?></li>
    		<li><?= __('New Website Visits'); ?></li>
    	</ul>
    	<p> <?= __('Additionally, tracking calls in the Lead Manager will also push their outcome &amp; details to your Follow Up Boss account.'); ?> </p>

	<?php } ?>

    </div>

</form>