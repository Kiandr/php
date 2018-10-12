<?php

// Display Pagination
if (!empty($pagination['links'])) {
	echo '<div class="pagination' . (!empty($pagination['extra']) ? ' ' . $pagination['extra'] : '') . '">' . PHP_EOL;
	if (!empty($pagination['prev'])) {
		echo '<a class="prev" rel="prev" href="' . $pagination['prev']['url'] . '">&#171;</a>' . PHP_EOL;
	}
	if (!empty($pagination['links'])) {
		foreach ($pagination['links'] as $link) {
			if (!empty($link['active'])) {
				echo '<a class="current" href="' . $link['url'] . '">' . $link['link'] . '</a>' . PHP_EOL;
			} else {
				echo '<a href="' . $link['url'] . '">' . $link['link'] . '</a>' . PHP_EOL;
			}
		}
	}
	if (!empty($pagination['next'])) {
		echo '<a class="next" rel="next" href="' . $pagination['next']['url'] . '">&#187;</a>' . PHP_EOL;
	}
	echo '</div>' . PHP_EOL;
}