<?php if (!empty($agent) || !empty($team_subdomain['agents'])) { ?>
	<div class="agent-details"<?=isset($agent['team'])?' hidden':''; ?>>
		<div class="wrap">
			<div class="wrap-inner">
				<?php if (isset($agent['team']) || !empty($team_subdomain['agents'])) { ?>
					<div class="carousel">
						<div class="slideset">
							<?php
                            if (!empty($team_subdomain['agents'])) {
                                $agents = $team_subdomain['agents'];
                            } else {
                                $agents = array_merge([$agent], $agent['team']['agents']);
                            }
                            ?>
							<?php foreach ($agents AS $k => $agent) {?>
								<div class="slide" data-slide="<?=$k; ?>">
									<?php if (!empty($agent['image'])) { ?>
										<img src="<?=$agent['image']; ?>" alt="">
									<?php } ?>
									<h3><?=Format::htmlspecialchars($agent['name']); ?></h3>
									<h4 class="small-caps">
									    <?=Format::htmlspecialchars($agent['team']['title'])?>
									    <?=!empty($agent['team']['title']) && !empty($agent['title']) ? ' - ' : ''; ?>
									    <?=Format::htmlspecialchars($agent['title']); ?>
								    </h4>
									<?php if (!empty($agent['office_phone']) || !empty($agent['cell_phone'])) { ?>
										<ul>
											<?php if (!empty($agent['office_phone'])) { ?>
												<li>
													<strong style="display: inline-block !important;">Office</strong>
													<span style="display: inline-block !important;"><?=Format::htmlspecialchars($agent['office_phone']); ?>
												</li>
											<?php } ?>
											<?php if (!empty($agent['cell_phone'])) { ?>
												<li>
													<strong style="display: inline-block !important;">Cell</strong>
													<span style="display: inline-block !important;"><?=Format::htmlspecialchars($agent['cell_phone']); ?></span>
												</li>
											<?php } ?>
										</ul>
									<?php } ?>
									<a href="<?=$listing['url_inquire']; ?>?agent=<?=$agent['id']; ?>" class="buttonstyle popup">Questions?</a>
								</div>
							<?php } ?>
						</div>
						<a class="prev" href="javascript:void(0);" hidden><i class="icon-chevron-left"></i></a>
						<a class="next" href="javascript:void(0);"><i class="icon-chevron-right"></i></a>
					</div>
				<?php } else { ?>
					<?php if (!empty($agent['image'])) { ?>
						<img src="<?=$agent['image']; ?>" alt="">
					<?php } ?>
					<h3><?=Format::htmlspecialchars($agent['name']); ?></h3>
					<h4 class="small-caps"><?=Format::htmlspecialchars($agent['title']); ?></h4>
					<?php if (!empty($agent['office_phone']) || !empty($agent['cell_phone'])) { ?>
						<ul>
							<?php if (!empty($agent['office_phone'])) { ?>
								<li>
									<strong>Office</strong>
									<span><?=Format::htmlspecialchars($agent['office_phone']); ?>
								</li>
							<?php } ?>
							<?php if (!empty($agent['cell_phone'])) { ?>
								<li>
									<strong>Cell</strong>
									<span><?=Format::htmlspecialchars($agent['cell_phone']); ?></span>
								</li>
							<?php } ?>
						</ul>
					<?php } ?>
					<a href="<?=$listing['url_inquire']; ?>?agent=<?=$agent['id']; ?>" class="buttonstyle popup">Questions?</a>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } ?>