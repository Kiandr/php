<?php

// Render lead summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
    'title' => 'Lead Visits',
    'lead' => $lead,
    'leadAuth' => $leadAuth
]);

?>

<div class="block">

<?php if (isset($_GET['popup'])) : ?>
<div class="btns"> <a class="btn" href="javascript:void(0);">Close</a> </div>
<?php endif; ?>
<?php $rnow = date('d-m-Y'); ?>
<?php if (!empty($sessions)) : ?>
<?php foreach ($sessions as $datestamp => $visits) : ?>
<h2 class="div">
	<?php

            					if ($rnow == date('d-m-Y', strtotime($datestamp))) {
            						echo 'Today';
            					} else if (date('d-m-Y', strtotime('-1 day')) == date('d-m-Y', strtotime($datestamp))) {
            							echo 'Yesterday';
            					} else {
            						echo date('D, F jS Y', strtotime($datestamp));
            					}

            				?>
</h2>
<?php if (!empty($visits)) : ?>
<?php foreach ($visits as $visit) : ?>
<article> <strong>
	<?php

                                                         echo 'Session Started';
                                                         if ($visit['ip'] != '0.0.0.0') {
                                                             $geoip = geoip_record_by_name($visit['ip']);
                                                             echo ' from <a href="' . URL_BACKEND . 'leads/?submit=true&search_ip=' . $visit['ip'] . '">' . $visit['ip'] . '</a>';
                                                             if (!empty($geoip['city'])) {
                                                                 echo ' (' . $geoip['city'] . ', ' . $geoip['region'] . ', ' . $geoip['country_name'] . ')';
                                                             } else if (!empty($geoip['region'])) {
                                                                 echo ' (' . $geoip['region'] . ', ' . $geoip['country_name'] . ')';
                                                             } else if (!empty($geoip['country_name'])) {
                                                                 echo ' (' . $geoip['country_name'] . ')';
                                                             }
                                                         }
                                                         if (!empty($visit['source'])) {
                                                             echo ', ' . $visit['source'];
                                                         }

                                                    ?>
	</strong>
	<?php if (!empty($visit['pages'])) : ?>
	<?php foreach( $visit['pages'] as $view) : ?>
	<article>
		<?php

                                                                    /* Shrink URL */
                                                                    $url_parts = parse_url($view['url']);
                                                                    $url = $url_parts['path'];
                                                                    $url .= (!empty($url_parts['query']))    ? '?' . $url_parts['query'] : '';
                                                                    $url .= (!empty($url_parts['fragment'])) ? '#' . $url_parts['fragment'] : '';

                                                                ?>
		<time>
			<?=date('g:ia', $view['date']); ?>
		</time>
		<span title="<?=$view['url']; ?>">
		<?=substr($url, 0, 50); ?>
		</span> </article>
	<?php endforeach; ?>
	</section>
	<?php endif; ?>
</article>
<?php endforeach; ?>
<?php endif; ?>
<?php endforeach; ?>
<?php else : ?>
<p class="block">This lead currently has no tracked visits.</p>
<?php endif; ?>
</div>
