<?php

// IDX Result Template
$result_tpl = Page::locateTemplate('idx', 'misc', 'result');

// Include IDX Result Template
if (!empty($results)) {
	echo '<div class="articleset listings' . (!empty($viewClass) ? ' '. $viewClass : '') . '">';
	foreach ($results as $result) {
		\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->load($result['idx']);

		// Only if not restricted to specific office
		if ((!empty($_COMPLIANCE['featured']['broker']) || !empty($_COMPLIANCE['featured']['office_id'])) && $this->config('mode') === 'featured') {
		    $_COMPLIANCE['results']['show_mls'] = (isset($_COMPLIANCE['featured']['show_mls']))? $_COMPLIANCE['featured']['show_mls'] : $_COMPLIANCE['results']['show_mls'];
		    $_COMPLIANCE['results']['show_icon'] = (isset($_COMPLIANCE['featured']['show_icon']))? $_COMPLIANCE['featured']['show_icon'] : $_COMPLIANCE['results']['show_icon'];
		    $_COMPLIANCE['results']['show_agent'] = (isset($_COMPLIANCE['featured']['show_agent']))? $_COMPLIANCE['featured']['show_agent'] : $_COMPLIANCE['results']['show_agent'];
		    $_COMPLIANCE['results']['show_office'] = (isset($_COMPLIANCE['featured']['show_office']))? $_COMPLIANCE['featured']['show_office'] : $_COMPLIANCE['results']['show_office'];
		    $_COMPLIANCE['results']['lang']['provider'] = (isset($_COMPLIANCE['featured']['lang']['provider']))? $_COMPLIANCE['featured']['lang']['provider'] : $_COMPLIANCE['results']['lang']['provider'];

		    $_REQUEST['snippet'] = false;
		}

		include $result_tpl;
	}
	echo '</div>';
}