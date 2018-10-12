<?php

if (!empty($pods) && is_array($pods)) {
	echo '<div id="sellers-landing">';
	foreach($pods as $pod) {
		echo $pod['markup'];
	}
	echo '</div>';
}