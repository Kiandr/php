<?php if (!empty($team)) { ?>
	<div id="agent-subdomain-header">
		<div class="wrap">
			<?php if (!empty($team->info('image'))) { ?>
				<div class="agent-subdomain-img">
					<img width="200" src="/thumbs/194x230/uploads/teams/<?=Format::htmlspecialchars($team->info('image')) ; ?>" alt="" />
				</div>
			<?php } ?>
			<div class="agent-subdomain-info">
				<h1><?=Format::htmlspecialchars($team->info('name')); ?></h1>
				<?php if ($this->config('homepage') && !empty($team->info('description'))) { ?>
					<hr /><p><?=Format::truncate($team->info('description'), 225); ?></p>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } ?>