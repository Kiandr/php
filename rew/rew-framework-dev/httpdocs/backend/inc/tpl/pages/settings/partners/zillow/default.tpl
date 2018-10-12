<form action="<?=$form_action;?>" method="post" class="rew_check">

    <div class="bar">
	    <div class="bar__title"><?= __('Zillow&reg;'); ?></div>
        <div class="bar__actions">
            <?php if (isset($_GET['setup'])) { ?>
                <a class="bar__action" href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>settings/partners/zillow/"><svg class="icon"><use xlink:href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>img/icos.svg#icon-left-a"/></svg></a>
            <?php } else { ?>
                <a class="bar__action" href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>settings/partners/"><svg class="icon"><use xlink:href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>img/icos.svg#icon-left-a"/></svg></a>
            <?php } ?>
        </div>
    </div>

    <div class="block">

	<div class="btns btns--stickyB"> <span class="R">
		<?php if (isset($account)) { ?>
		    <a class="btn import" href="?import" onclick="javascript:return confirm('<?= __('Are you sure you want to import all leads from your Zillow integration?'); ?>');"><?= __('Import'); ?></a>
		    <a class="btn delete" href="?delete" onclick="javascript:return confirm('<?= __('Are you sure you want to disable the integration with this partner?'); ?>');"><?= __('Disable'); ?></a>
            <button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
		<?php } ?>
	</span> </div>
	<?php if (!isset($account)) { ?>
	<div class="help"> <img src="/backend/img/hlp/setup.png"/>
		<h1><?= __('Set Up Zillow&reg; Integration'); ?></h1>
		<p><?= __('Zillow&reg; integration is currently %s. To use this feature, generate an integration key (by clicking the button below) and provide it to zillow.', '<strong>' . __('inactive') . '</strong>'); ?></p>
		<p><button class="btn btn--positive" type="submit"><svg class="icon icon-add mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg> <?= __('Set Up Zillow&reg; Integration'); ?></button></p>
	</div>
	<?php } else { ?>

        <div class="field">
           <div>
               <label class="field__label"><?= __('Agent GUID'); ?></label>
                <b><?=htmlspecialchars($account['guid']); ?></b>
               <p class="tip"><?= __('This GUID can be provided to your Zillow account on the Connect to My CRM page under the Contacts Tab.'); ?></p>
            </div>
            <div>
                <label class="field__label"><?= __('Installation Instructions'); ?></label>
                <ol>
                    <li><?= __('Logs into your Zillow Agent Hub'); ?></li>
                    <li><?= __('Clicks on “Contacts” and then “Connect to My CRM”'); ?></li>
                    <li><?= __('Clicks on “Add Partner”'); ?></li>
                    <li><?= __('Select "Real Estate Webmasters" from partners list'); ?></li>
                    <li><?= __('Input the above GUID in the "Agent GUID" field'); ?></li>
                    <li><?= __('Click Save'); ?></li>
                </ol>
            </div>
		</div>
    </div>
    <div class="bar">
        <div class="bar__title"><?= __('Zillow Settings'); ?></div>
    </div>
    <div class="block">
        <div class="field">
            <label class="field__label"><?= __('Use Global Assignment Settings'); ?></label>
            <label for="global_assignment_true">
                <input type="radio" name="global_assignment" id="global_assignment_true" value="true"<?=$global_assignment ? ' checked' : ''; ?>>
                <span class="toggle__label"><?= __('On'); ?></span>
            </label>
            <label for="global_assignment_false">
                <input type="radio" name="global_assignment" id="global_assignment_false" value="false"<?=!$global_assignment ? ' checked' : ''; ?>>
                <span class="toggle__label"><?= __('Off'); ?></span>
            </label>
            <p class="text--mute"><?= __('If this is on, new leads received from Zillow under this Agent GUID will follow the global assignment settings.'); ?></p>
        </div>
    </div>
	<?php } ?>



</form>
