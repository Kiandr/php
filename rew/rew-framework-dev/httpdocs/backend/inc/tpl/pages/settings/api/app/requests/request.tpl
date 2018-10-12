<div class="block">
    <div class="bar -padL0 -padR0">
        <h1 class="bar__title mar0 -padL0 -padR0" style="font-weight: normal;">
            <?php if (!empty($request)) { ?>
            <?=htmlspecialchars($request['method']);?>
            <?=htmlspecialchars(Format::truncate($request['uri'], 170));?>
            <?php } else { ?>
            <?= __('View API Request'); ?>
            <?php } ?>
        </h1>
        <div class="bar__actions">
            <a class="bar__action" href="../?id=<?=htmlspecialchars($application['id']);?>">
                <svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg>
            </a>
        </div>
    </div>

    <div class="request-data">
    	<?php if (!empty($headers)) { ?>
    	<h2><?= __('Headers'); ?></h2>
    	<dl class="request__list">
    		<?php foreach ($headers as $key => $value) { ?>
    		<dt class="request__key">
    			<?=htmlspecialchars($key);?>
    		</dt>
    		<dd class="request__value">
    			<?=!empty($value) ? htmlspecialchars($value) : '&nbsp;';?>
    		</dd>
    		<?php } ?>
    	</dl>
    	<?php }  ?>
    	<?php if (!empty($get)) { ?>
    	<h2><?= __('GET Parameters'); ?></h2>
    	<dl class="request__list">
    		<?php foreach ($get as $key => $value) { ?>
    		<dt class="request__key">
    			<?=htmlspecialchars($key);?>
    		</dt>
    		<dd class="request__value">
    			<?php if (is_array($value)) { ?>
    			<pre><?=print_r($value, true);?></pre>
    			<?php } else { ?>
    			<?=!empty($value) ? htmlspecialchars($value) : '&nbsp;';?>
    			<?php } ?>
    		</dd>
    		<?php } ?>
    	</dl>
    	<?php }  ?>
    	<?php if (!empty($post)) { ?>
    	<h2><?= __('POST Parameters'); ?></h2>
    	<dl class="request__list">
    		<?php foreach ($post as $key => $value) { ?>
    		<dt class="request__key">
    			<?=htmlspecialchars($key);?>
    		</dt>
    		<dd class="request__value">
    			<?php if (is_array($value)) { ?>
    			<pre><?=print_r($value, true);?>
    </pre>
    			<?php } else { ?>
    			<?=!empty($value) ? htmlspecialchars($value) : '&nbsp;';?>
    			<?php } ?>
    		</dd>
    		<?php } ?>
    	</dl>
    	<?php }  ?>
    </div>
    <?php if (!empty($request['response'])) { ?>
    <h2><?= __('Response'); ?></h2>
    <pre><?=htmlspecialchars($request['response']);?>
    </pre>
    <?php } ?>
    <div class="request-data">
    	<h2><?= __('Request Summary'); ?></h2>
    	<dl class="request__list">
    		<?php if (!empty($request['user_agent'])) { ?>
    		<dt class="request__key"><?= __('User Agent'); ?></dt>
    		<dd class="request__value">
    			<?=htmlspecialchars($request['user_agent']);?>
    		</dd>
    		<?php } ?>
    		<dt class="request__key"><?= __('IP'); ?></dt>
    		<dd class="request__value">
    			<?=htmlspecialchars($request['ip']);?>
    		</dd>
    		<dt class="request__key"><?= __('Duration'); ?></dt>
    		<dd class="request__value">
    			<?=htmlspecialchars($request['duration']);?>
    		</dd>
    		<dt class="request__key"><?= __('Created'); ?></dt>
    		<dd class="request__value">
    			<time datetime="<?=date('c', $request['timestamp_created']); ?>" title="<?=date('l, F jS Y \@ g:ia', $request['timestamp_created']); ?>">
    				<?=Format::dateRelative($request['timestamp_created']); ?>
    			</time>
    		</dd>
    		<dt class="request__key"><?= __('Status'); ?></dt>
    		<dd class="request__value">
    			<?php if ($request['status'] === 'ok') { ?>
    			<label class="group group_i"><?= __('OK'); ?></label>
    			<?php } else { ?>
    			<label class="group group_a">
    				<?=htmlspecialchars(ucwordS($request['status']));?>
    			</label>
    			<?php } ?>
    		</dd>
    	</dl>
    </div>
</div>