<?php

// Display feed links
if (!empty($feeds)) {
	echo '<ul class="feed-switcher">';
	foreach ($feeds as $feed) {
		if (!empty($feed['active'])) {
			echo '<li class="current"><a href="' . Format::htmlspecialchars($feed['link']) . '">' . Format::htmlspecialchars($feed['title']) . '</a></li>';
		} else {
			echo '<li><a href="' . Format::htmlspecialchars($feed['link']) . '">' . Format::htmlspecialchars($feed['title']) . '</a></li>';
		}
	}
	echo '</ul>';
}