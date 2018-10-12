<?php if (isset($_GET['popup'])) return; ?>
<?php $user = User_Session::get(); ?>
<div class="idx-user-bar">
	<?php if (!empty($user) && $user->isValid()) { ?>
		<p>Welcome, <?=Format::htmlspecialchars($user->info('first_name')); ?>!</p>
		<div class="nav horizontal">
			<h4><a href="/idx/dashboard.html" class="popup">Dashboard</a></h4>
			<ul class="hidden-tablet hidden-phone">
				<li><a href="/idx/dashboard.html" class="popup"><?=Locale::spell('Favorites'); ?></a></li>
				<li><a href="/idx/dashboard.html?type=searches" class="popup">Saved Searches</a></li>
				<li><a href="/idx/dashboard.html?type=messages" class="popup">Messages</a></li>
				<li><a href="/idx/dashboard.html?type=preferences" class="popup">Preferences</a></li>
				<li><a href="/idx/logout.html">Sign Out</a></li>
			</ul>
		</div>
	<?php } else { ?>
		<div class="nav horizontal">
			<ul>
				<li><a href="/idx/register.html" class="popup">Sign Up</a></li>
				<li><a href="/idx/login.html" class="popup">Sign In</a></li>
			</ul>
		</div>
	<?php } ?>
	<div class="nav horizontal hidden-desktop">
		<ul>
			<li><span class="var-phone-number"><?php rew_snippet('var-phone-number'); ?></span></li>
		</ul>
	</div>
</div>