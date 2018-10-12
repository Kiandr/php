<?php if (!$user->isValid()) { ?>
	<div id="dialog-login" class="hidden">
		<header>
			<h4 class="text-center">
				Sign In Instantly
				<?php if (!empty($networks)) { ?>
					<small class="block">With Social Media</small>
				<?php } ?>
			</h4>
		</header>
		<?php if (!empty($networks)) { ?>
			<ul class="social-connect">
				<?php foreach ($networks as $id => $network) { ?>
					<li class="network-<?=$id; ?>">
						<a title="Sign In using <?=Format::htmlspecialchars($network['title']); ?>" rel="nofollow" onclick="javascript:var w = window.open('<?=$network['connect']; ?>', 'socialconnect', 'toolbar=0,status=0,scrollbars=1,width=600,height=450,left='+(screen.availWidth/2-225)+',top='+(screen.availHeight/2-250)); w.focus();">
							<?=Format::htmlspecialchars($network['title']); ?>
						</a>
					</li>
				<?php } ?>
			</ul>
		<?php } ?>
		<form action="<?=Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']; ?>?login" method="post">
			<input type="email" name="email" value="<?=htmlentities($user->info('email')); ?>" placeholder="Email Address" required>
			<?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
				<input type="password" name="password" placeholder="Password" required>
			<?php } ?>
			<button type="submit">Sign In</button>
		</form>
		<p>Not a Member? <a class="popup" href="<?=Settings::getInstance()->SETTINGS['URL_IDX_REGISTER']; ?>">Sign up Now</a></p>
	</div>
<?php } ?>