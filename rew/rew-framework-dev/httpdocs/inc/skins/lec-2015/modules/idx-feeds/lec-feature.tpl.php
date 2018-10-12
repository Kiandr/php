<?php

// IDX feed switcher
if (!empty($feeds)) {
	echo '<ul id="' . $this->getUID() . '" class="feed-switcher">';
	foreach ($feeds as $feed) {
		echo '<li' . (!empty($feed['active']) ? ' class="current"' : '') . '>';
		echo '<a data-feed="' . htmlspecialchars(json_encode(array('link' => $feed['link'], 'name' => $feed['name']))) . '">';
		echo Format::htmlspecialchars($feed['title']);
		echo '</a>';
		echo '</li>';
	}
	echo '</ul>';

}