<?php if (!empty($agent)) { ?>
	<div id="agent-subdomain-header">
		<div class="wrap">
			<?php if (!empty($agent['image'])) { ?>
				<div class="agent-subdomain-img">
					<img width="200" src="/thumbs/194x230/uploads/agents/<?=Format::htmlspecialchars($agent['image']) ; ?>" alt="" />
				</div>
			<?php } ?>
			<div class="agent-subdomain-info">
				<h1><?=Format::htmlspecialchars($agent['name']); ?></h1>
				<?php if ($this->config('homepage') && !empty($agent['remarks'])) { ?>
					<hr /><p><?=Format::truncate($agent['remarks'], 225); ?></p>
				<?php } ?>
				<span>
					<?=implode(' | ', array_filter(array(
						(!empty($agent['cell_phone']) ? 'Cell: ' . Format::htmlspecialchars($agent['cell_phone']) : NULL),
						(!empty($agent['office_phone']) ? 'Office: ' . Format::htmlspecialchars($agent['office_phone']) : NULL),
					))); ?>
				</span>
			</div>
		</div>
	</div>
<?php } ?>