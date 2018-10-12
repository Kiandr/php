<?php $url = Settings::getInstance()->SETTINGS['URL_RAW'] . $_SERVER['REQUEST_URI']; ?>
<div id="sm-slideout">
	<a class="sm-slide">Connect</a>
	<div id="sm-slideout-wrap">
		<?php rew_snippet('social-media'); ?>
		<?php if (!empty($user) && $user->isValid()) { ?>
			<h3>Welcome, <?=Format::htmlspecialchars($user->info('first_name')); ?>!</h3>
			<div class="nav">
				<ul>
					<li><a class="slideout-link popup icon-cog" href="/idx/dashboard.html">Dashboard</a></li>
					<li><a class="slideout-link popup icon-star" href="/idx/dashboard.html"><?=Locale::spell('Favorites'); ?></a></li>
					<li><a class="slideout-link popup icon-save" href="/idx/dashboard.html?view=searches">Saved Searches</a></li>
					<li><a class="slideout-link popup icon-comment" href="/idx/dashboard.html?view=messages">Messages</a></li>
					<li><a class="slideout-link popup icon-gears" href="/idx/dashboard.html?view=preferences">Preferences</a></li>
					<li><a class="slideout-link icon-signout" href="/idx/logout.html">Sign Out</a></li>
				</ul>
			</div>
		<?php } else { ?>
			<h3>Dashboard</h3>
			<div class="nav">
				<ul>
					<li><a class="slideout-link popup icon-pencil" href="/idx/register.html">Register</a></li>
					<li><a class="slideout-link popup icon-signin" href="/idx/login.html">Sign In</a></li>
				</ul>
			</div>
			<?php if (!empty($networks)) { ?>
				<h3>Login using...</h3>
				<div class="networks">
					<ul>
						<?php foreach ($networks as $id => $network) { ?>
							<li>
								<a class="network-login <?=$id; ?>" href="javascript:var w = window.open('<?=$network['connect']; ?>', 'socialconnect', 'toolbar=0,status=0,scrollbars=1,width=600,height=450,left='+(screen.availWidth/2-225)+',top='+(screen.availHeight/2-250)); w.focus();"></a>
							</li>
						<?php } ?>
					</ul>
				</div>
			<?php } ?>
		<?php } ?>
	</div>
</div>