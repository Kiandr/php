<?php

// Require Composer Vendor Auto loader
require_once $_SERVER['DOCUMENT_ROOT'] . '/../boot/app.php';

// Require module
if (empty(Settings::getInstance()->MODULES['REW_CRM_API'])) {
	header('Location: ' . Settings::getInstance()->SETTINGS['URL']);
	exit;
}

/**
 * Handle IP if running via proxy
 */
if (isset($_SERVER['HTTP_X_REAL_IP'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
}

// Validate API Key
$api_key_valid = false;
if (!empty($_SERVER['PHP_AUTH_PW'])) {
	try {
		$db = DB::get('users');
		if ($application = $db->{'api_applications'}->search(array('$eq' => array('api_key' => $_SERVER['PHP_AUTH_PW'], 'enabled' => 'Y')))->fetch()) {
			$api_key_valid = true;
		}
	} catch (Exception $ex) {
		Log::error($ex);
	}
}

// Require auth
$username = 'crm';

// Check credentials
if ($_SERVER['PHP_AUTH_USER'] !== $username || empty($api_key_valid)) {
    header('WWW-Authenticate: Basic realm="The username is \'crm\' and the password is your Application\'s API Key"');
    header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');

    // Cancel text
    echo 'You must authenticate before you can view this page. The username is \'crm\' and the password is your Application\'s API Key.';
    exit;
}

?><!DOCTYPE html>
<html>
<head>
	<title>API Developer Reference</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="inc/css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="inc/css/docs.css" rel="stylesheet" media="screen">
	<link href="inc/lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>

	<div class="navbar navbar-fixed-top docs-nav">
		<div class="container">
			<a class="navbar-brand" href=""><i class="icon-code"></i> API Developer Reference</a>

			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a href="">CRM API</a></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="row">
			<div class="col-lg-3">
				<div class="docs-sidebar">
					<ul class="nav docs-sidenav">
						<li class="dropdown-header">API</li>
						<li><a href="#overview">Overview</a></li>
						<li><a href="#authentication">Authentication</a></li>
						<li>
							<a href="#responses">Responses</a>
							<ul class="nav">
								<li><a href="#responses-codes">Status Codes</a></li>
								<li><a href="#responses-success">Success</a></li>
								<li><a href="#responses-error">Error</a></li>
							</ul>
						</li>

						<li class="dropdown-header">Methods</li>
						<li>
							<a href="#agents">Agents</a>
							<ul class="nav">
								<li><a href="#agents-list">List all agents</a></li>
							</ul>
						</li>
						<li>
							<a href="#favorites"><?=Locale::spell('Favorites');?></a>
							<ul class="nav">
								<li><a href="#favorites-create">Create a new <?=Locale::spell('favorite');?></a></li>
								<li><a href="#favorites-retrieve">Retrieve an existing <?=Locale::spell('favorite');?></a></li>
								<li><a href="#favorites-delete">Delete a <?=Locale::spell('favorite');?></a></li>
								<li><a href="#favorites-list">List all <?=Locale::spell('favorites');?></a></li>
							</ul>
						</li>
						<li>
							<a href="#groups">Groups</a>
							<ul class="nav">
								<li><a href="#groups-create">Create a new group</a></li>
								<li><a href="#groups-retrieve">Retrieve an existing group</a></li>
								<li><a href="#groups-delete">Delete a group</a></li>
								<li><a href="#groups-list">List all groups</a></li>
							</ul>
						</li>
						<li>
							<a href="#leads">Leads</a>
							<ul class="nav">
								<li><a href="#leads-create">Create a new lead</a></li>
								<li><a href="#leads-retrieve">Retrieve an existing lead</a></li>
								<li><a href="#leads-update">Update a lead</a></li>
                                <li><a href="#leads-upsert">Create or Update a lead</a></li>
							</ul>
						</li>
						<li>
							<a href="#events">History Events</a>
							<ul class="nav">
								<li><a href="#events-create">Create a new event</a></li>
							</ul>
						</li>
						<li>
							<a href="#searches">Saved Searches</a>
							<ul class="nav">
								<li><a href="#searches-create">Create a new saved search</a></li>
								<li><a href="#searches-retrieve">Retrieve an existing saved search</a></li>
								<li><a href="#searches-update">Update a saved search</a></li>
								<li><a href="#searches-delete">Delete a saved search</a></li>
								<li><a href="#searches-list">List all saved searches</a></li>
							</ul>
						</li>
                        <li>
                            <a href="#notes">Notes</a>
                            <ul class="nav">
                            <li><a href="#notes-create">Create a new note</a></li>
                            </ul>
                        </li>
						<li>
							<a href="#hooks">Hooks</a>
							<ul class="nav">
								<li><a href="#hooks-trigger">Trigger hook</a></li>
							</ul>
						</li>
						<?php if (Settings::isREW()) { ?>
						<li>
							<a href="#instant_searches">Instant Searches</a>
							<ul class="nav">
								<li><a href="#instant_searches-list">List Instant Searches</a></li>
								<li><a href="#instant_searches-email">Email Search Results</a></li>
							</ul>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<div class="col-lg-9">

				<!-- Overview -->
				<?php include __DIR__ . '/tpl/api/overview.tpl';?>

				<!-- Authentication -->
				<?php include __DIR__ . '/tpl/api/authentication.tpl';?>

				<!-- Responses -->
				<?php include __DIR__ . '/tpl/api/responses.tpl';?>

				<!-- Agents -->
				<?php include __DIR__ . '/tpl/methods/agents.tpl';?>

				<!-- Favorites -->
				<?php include __DIR__ . '/tpl/methods/favorites.tpl';?>

				<!-- Groups -->
				<?php include __DIR__ . '/tpl/methods/groups.tpl';?>

				<!-- Leads -->
				<?php include __DIR__ . '/tpl/methods/leads.tpl';?>

				<!-- History Events -->
				<?php include __DIR__ . '/tpl/methods/events.tpl';?>

				<!-- Saved Searches -->
				<?php include __DIR__ . '/tpl/methods/searches.tpl';?>

                <!-- Notes -->
                <?php include __DIR__ . '/tpl/methods/notes.tpl';?>

				<!-- Hooks -->
				<?php include __DIR__ . '/tpl/methods/hooks.tpl';?>

				<!-- Instant Searches -->
				<?php if (Settings::isREW()) {
					include __DIR__ . '/tpl/methods/instant_searches.tpl';
				} ?>

			</div>
		</div>

		<footer class="docs-footer">
			<p class="pull-right">
				<a href="#">Back to top</a>
			</p>
			<p>
				&copy; <?=date('Y');?> Real Estate Webmasters Inc.
			</p>
		</footer>
	</div>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="inc/js/bootstrap.min.js"></script>
	<script src="//google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>

	<script>

		// Scrollspy
		$(document.body).scrollspy({
			target: '.docs-sidebar',
			offset: 70
		});

		// Sidebar
		setTimeout(function () {
			var $sideBar = $('.docs-sidebar');
			$sideBar.affix({
				offset: {
					top: function () {
						var offsetTop      = 0,
							sideBarMargin  = parseInt($sideBar.children(0).css('margin-top'), 10),
							navOuterHeight = $('.docs-nav').height();

						return (this.top = offsetTop - navOuterHeight - sideBarMargin);
					}
					, bottom: function () {
						return (this.bottom = $('.docs-footer').outerHeight(true));
					}
				}
			});
		}, 100);

		// Anchor links
		$('a[href^="#"]').on('click', function () {
			var $target = $(this.hash);
			if (!$target.length) return;

			$('html,body').animate({
				scrollTop : $target.offset().top
			}, {
				duration : 300,
				queue : false
			});
			return true;
		});

	</script>
</body>
</html>
