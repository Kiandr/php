<form action="?submit" method="post" class="rew_check">

	<div class="block">
        <div class="bar">
            <h1 class="bar__title mar0 -padL0 -padR0" style="font-weight: normal;"><?= __('Add New Destination'); ?></h1>
            <div class="bar__actions">
                <a href="/backend/settings/api/outgoing/?back" class="bar__action">
                    <svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg>
                </a>
            </div>
        </div>
		<div class="btns btns--stickyB"> <span class="R">
			<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
			</span>
		</div>
        <h3 class="divider">
            <span class="divider__label divider__label--left"><?= __('General Information'); ?></span>
        </h3>
		<div class="field">
			<label class="field__label" for="app_name"><?= __('Name'); ?> <em class="required">*</em></label>
			<input class="w1/1" id="app_name" type="text" name="name" value="<?=htmlspecialchars($_POST['name']);?>" placeholder="<?= __('My Push Destination'); ?>">
			<label class="text--small text--mute"> <?= __('This is for your own reference. Example: MyThirdPartySite.com'); ?> </label>
		</div>
		<div class="field">
			<label class="field__label"><?= __('Destination Type'); ?></label>
			<div class="toggle">
				<input type="radio" id="type_rew" name="type" value="rew" <?=(($_POST['type'] === 'rew') ? ' checked="checked"' : ''); ?>>
				<label for="type_rew" class="toggle__label"><?= __('REW Backend'); ?></label>
				<input type="radio" id="type_custom" name="type" value="custom" <?=(($_POST['type'] !== 'rew') ? ' checked="checked"' : ''); ?>>
				<label for="type_custom" class="toggle__label"><?= __('Custom (Third-Party)'); ?></label>
			</div>
		</div>
		<div class="fieldset-group" data-group-name="rew" <?=$_POST['type'] !== 'rew' ? 'style="display: none;"' : '';?>>
		<h3 class="panel__hd"><?= __('REW Backend Information'); ?></h3>
		<p> <?= __('You\'ve specified that the destination is another REW site. We already know how to push events to that type of destination - just provide the API Key &amp; URL of the site below:'); ?> </p>
		<div class="field">
			<label class="field__label" for="rew_url"><?= __('URL'); ?> <em class="required">*</em></label>
			<input class="w1/1" id="rew_url" type="text" name="rew_url" value="<?=htmlspecialchars($_POST['rew_url']);?>" placeholder="<?= __('http://www.mymainrewsite.com/'); ?>">
			<label class="text--small text--mute"> <?= __('This is the URL of the REW site that will be receiving the events.'); ?> </label>
		</div>
		<div class="field">
			<label class="field__label" for="rew_api_key"><?= __('API Key'); ?> <em class="required">*</em></label>
			<input class="w1/1" id="rew_api_key" type="text" name="rew_api_key" value="<?=htmlspecialchars($_POST['rew_api_key']);?>">
			<label class="text--small text--mute"> <?= __('This is the API Key of an API Application that you\'ve created on the destination REW site.'); ?> </label>
		</div>
        <h3 class="divider">
            <span class="divider__label divider__label--left"><?= __('Push Events'); ?></span>
        </h3>
		<p> <?= __('Notify the Destination when the following events occur within this site:'); ?> </p>
		<div class="field">
			<ul style="list-style: none; padding-left: 0; margin: 0;">
				<?php foreach (Hook_REW_OutgoingAPI::getSupportedEventsForDestination(Hook_REW_OutgoingAPI::DESTINATION_TYPE_REW) as $event) { ?>
				<li>
					<label class="toggle -marB8">
						<?php $checked = !isset($_POST['rew_events']) && $_SERVER['REQUEST_METHOD'] == 'GET' ? 'checked' : (is_array($_POST['rew_events']) && in_array($event['value'], $_POST['rew_events']) ? 'checked' : ''); ?>
						<input <?=$checked;?> type="checkbox" name="rew_events[]" value="<?=htmlspecialchars($event['value']);?>">
						<span class="toggle__label"><?=htmlspecialchars($event['title']);?></span>
					</label>
				</li>
				<?php } ?>
			</ul>
		</div>
		</div>
		<div data-group-name="custom" <?=$_POST['type'] !== 'custom' ? 'style="display: none;"' : '';?>>
			<div class="field">
				<h3 class="panel__hd"><?= __('Custom Destination Information'); ?></h3>
				<p> <?= __('You\'ve specified that the destination is third-party site. Provide that site\'s URL &amp; the events you\'d like to send to it:'); ?> </p>
				<div class="field">
					<label class="field__label" for="custom_url"><?= __('URL'); ?></label>
					<input class="w1/1" id="custom_url" type="text" name="custom_url" value="<?=htmlspecialchars($_POST['custom_url']);?>" placeholder="<?= __('http://www.mythirdpartysite.com/incoming'); ?>">
					<label class="text--small text--mute"> <?= __('This is the URL of the third-party site that will be receiving the events.'); ?> </label>
				</div>
			</div>
            <h3 class="divider">
                <span class="divider__label divider__label--left"><?= __('Push Events'); ?></span>
            </h3>
			<p> <?= __('Notify the Destination at the specified URLs when the following events occur within this site:'); ?> </p>
			<?php foreach (Hook_REW_OutgoingAPI::getSupportedEventsForDestination(Hook_REW_OutgoingAPI::DESTINATION_TYPE_CUSTOM) as $event) { ?>
			<div class="field fieldset-custom-destination">
				<label class="toggle">
					<?php $checked = (is_array($_POST['custom_events']) && $_POST['custom_events'][$event['value']]['enabled'] === 'Y' ? 'checked' : ''); ?>
					<input <?=$checked;?> type="checkbox" name="custom_events[<?=$event['value'];?>][enabled]" value="Y">
					<span class="toggle__label"><?=htmlspecialchars($event['title']);?></span>
				</label>
				<div class="field" <?=(is_array($_POST['custom_events']) && $_POST['custom_events'][$event['value']]['enabled'] === 'Y' ? '' : 'style="display:none;"');?>>
					<input class="w1/1" type="text" name="custom_events[<?=$event['value'];?>][url]" value="<?=$_POST['custom_events'][$event['value']]['url'];?>" placeholder="<?=htmlspecialchars($event['placeholder']);?>">
					<label class="text--small text--mute"> <?= __('POST http://www.mythirdpartysite.com/incoming'); ?>
						<?=htmlspecialchars($event['placeholder']);?>
						<br>
					</label>
				</div>
			</div>
			<?php } ?>
		</div>
        <h3 class="divider">
            <span class="divider__label divider__label--left"><?= __('Settings'); ?></span>
        </h3>
		<div>
			<label class="field__label"><?= __('Active'); ?></label>
			<div class="toggle">
				<input type="radio" id="enabled_Y" name="enabled" value="Y" <?=(($_POST['enabled'] === 'Y') ? ' checked="checked"' : ''); ?>>
				<label for="enabled_Y" class="toggle__label"><?= __('Yes'); ?></label>
				<input type="radio" id="enabled_N" name="enabled" value="N" <?=(($_POST['enabled'] !== 'Y') ? ' checked="checked"' : ''); ?>>
				<label for="enabled_N" class="toggle__label"><?= __('No'); ?></label>
			</div>
		</div>
		</div>
	</div>
</form>
