<?php

// Render lead summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
    'title' => 'Send Text Message',
    'lead' => $lead,
    'leadAuth' => $leadAuth
]);

?>

<?php if ($leadWarning) {?>
    <div class="block">
        <div class="warning hint"><?=htmlspecialchars($leadWarning); ?></div>
    </div>
<?php }?>

<form action="?id=<?=$lead['id']; ?>&submit" method="post" class="rew_check">

	<?php if (!empty($_GET['post_task'])) { ?>
		<input type="hidden" name="post_task" value="<?=Format::htmlspecialchars($_GET['post_task']); ?>">
	<?php } ?>
    <div class="block">
			<div class="btns btns--stickyB"><span class="R">
				<button class="btn btn--positive" type="submit">Send</button>
				<?php if (isset($_GET['popup']) || isset($_POST['popup'])) { ?>
					<a class="btn close" href="javascript:void(0);">Cancel</a>
				<?php } ?></span>
			</div>

				<div class="field">
					<h3>Send to Primary Number <em class="required">*</em></h3>
					<div id="to-number">
						<input class="w1/1" type="tel" name="to" value="<?=Format::htmlspecialchars($to); ?>" placeholder="<?=$phoneUtil->getExampleNumber('US'); ?>" required>
						<?php

							// Phone number error
							if (!empty($phone_error)) {
								echo PHP_EOL . '<div class="flag flag--negative"><span class="ico"></span> ' . $phone_error . '</div>';
							} elseif (!empty($phone_check['optout'])) {
								echo PHP_EOL . '<div class="flag flag--negative" title="' . Format::dateRelative($phone_check['optout']) . '"><span class="ico"></span> Opt-Out &ndash; Cannot Text</div>';
							} elseif (!empty($phone_check['verified'])) {
								echo PHP_EOL . '<div class="flag flag--positive" title="' . Format::dateRelative($phone_check['verified']) . '"><span class="ico"></span> Verified</div>';
							} else {
								echo PHP_EOL . '<div class="flag flag--negative"><span class="ico"></span> Unverified</div>';
							}

							// Checked the box to opt-in
							if ($lead['opt_texts'] === 'in' && empty($phone_check['optout'])) {
								echo PHP_EOL . '<div class="flag flag--positive"><span class="ico"></span> Opt-In</div>';
							}

						?>
					</div>
				</div>

				<div class="field">
					<h3>Your Text Message <em class="required">*</em></h3>
					<textarea class="w1/1" name="body" rows="4" maxlength="<?=$maxlength; ?>"<?=(!$media ? ' required' : ''); ?>><?=Format::htmlspecialchars($_POST['body']); ?></textarea>
					<!-- <label class="hint"><em class="charsRemaining">&nbsp;</em></label> -->
					<div class="hint marT marB"><strong>Available Tags:</strong> {first_name}, {last_name}</div>
				</div>

					<button id="attach-media" type="button" class="btn btn--positive <?=($media ? ' hidden' : ''); ?>">Attach an Image</button>
					<?php

						// Media attachment
						if (!empty($media)) {
							echo '<div class="attached-media">';
							echo '<input type="hidden" name="media" value="' . $media . '">';
							echo '<img src="' . $media . '" alt="">';
							echo '<em>(click image to remove)</em>';
							echo '</div>';
						}

					?>

    </div>
</form>