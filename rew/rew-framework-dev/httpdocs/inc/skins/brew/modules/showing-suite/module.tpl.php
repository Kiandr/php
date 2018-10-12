<?php if (!empty($display)) { ?>

	<div id="showingSuiteWrap">

		<script type="text/javascript" src="http://new.showingsuite.com/scripts/calendar-widget.js"></script>
		<script type="text/javascript">

			(function() {

				// Detect Mobile Device
				var isMobile = {
				    Android: function() { return navigator.userAgent.match(/Android/i); },
				    BlackBerry: function() { return navigator.userAgent.match(/BlackBerry/i); },
				    iOS: function() { return navigator.userAgent.match(/iPhone|iPad|iPod/i); },
				    Opera: function() { return navigator.userAgent.match(/Opera Mini/i); },
				    Windows: function() { return navigator.userAgent.match(/IEMobile/i); },
				    any: function() { return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows()); }
				},
				_swc = new showingSuiteCalendarWidget();

				// Force Mobile View Accordingly (Showing Suite Calendar Wasn't Built Responsively)
				if (isMobile.any()) _swc.forceMobile = '1';

				_swc.maincolor = '484848';
				_swc.secondarycolor = '0d2a43';
				_swc.thirdcolor = "fff"; // Background Colour
				_swc.font = "DroidSerifRegular";
				_swc.height = 2600;
				_swc.width = '100%';
				_swc.clientid = '<?=$ss_email; ?>';
		    	<?php if (!empty($user) && $user->isValid()) { ?>
		    		_swc.setUser(
						'<?=htmlspecialchars($user->info('first_name')); ?>',
						'<?=htmlspecialchars($user->info('last_name')); ?>',
						'<?=htmlspecialchars($user->info('email')); ?>',
						'<?=htmlspecialchars($user->info('phone')); ?>'
					);
		    	<?php } ?>
				_swc.showCalendarWidget('<?=htmlspecialchars($mls_num); ?>');

			})();

		</script>

	</div>

<?php } ?>