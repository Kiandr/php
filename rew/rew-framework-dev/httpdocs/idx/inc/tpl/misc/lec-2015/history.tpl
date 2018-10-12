<?php if (!empty($history)) { ?>
	<div id="listing-history">
		<h3>Price &amp; Status History</h3>
		<table>
			<thead>
				<tr>
					<th>Date</th>
					<th>Details</th>
					<th class="hidden-sm">Change</th>
				</tr>
			</thead>
			<?php $total = count($history); ?>
			<?php $chunks = array_chunk($history, 5); ?>
			<?php foreach ($chunks as $i => $history) { ?>
				<tbody<?=($i !== 0) ? ' class="extra hidden"' : ''; ?>>
					<?php foreach ($history as $j => $change) { ?>
						<tr>
							<th><time datetime="<?=date('c', $change['Date']);?>" title="<?=date('D\. M. jS\, Y', $change['Date']); ?>"><?=date('m/d/Y', $change['Date']); ?></time></th>
							<td><?=$change['Details']; ?></td>
							<?php if ($change['Type'] === 'Price') { ?>
								<td class="<?=($change['Diff'] < 0 ? 'neg': 'pos'); ?> hidden-sm">
									<i class="icon-arrow-<?=($change['Diff'] < 0 ? 'down' : 'up'); ?>"></i>
									<?php $multi = $change['Diff'] < 0 ? -1 : 1; ?>
									$<?=Format::number($change['Diff'] * $multi); ?>
									<?php if (!empty($change['Old'])) { ?>
										(<?=Format::number(($change['Diff'] / $change['Old']) * 100 * $multi, 2); ?>%)
									<?php } ?>
								</td>
							<?php } else { ?>
								<td class="hidden-sm">&ndash;</td>
							<?php } ?>
						</tr>
					<?php } ?>
					<?php if ($i === 0 && count($chunks) > 1) { ?>
						<tr>
							<td colspan="4"><a href="#more" onclick="$(this).closest('table').find('.hidden').removeClass('hidden'); $(this).closest('tr').remove(); return false;">Show More (<?=Format::number($total - count($chunks[0])); ?>)</a></td>
						</tr>
					<?php } ?>
				</tbody>
			<?php } ?>
		</table>
	</div>
<?php } ?>