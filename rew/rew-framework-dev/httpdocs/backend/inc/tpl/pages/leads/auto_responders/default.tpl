<div class="bar">
    <div class="bar__title"><?= __('Responders'); ?></div>
</div>

<?php if (!empty($autoresponders)) : ?>
<div class="nodes">
    <ul class="nodes__list">
    	<?php foreach ($autoresponders as $autoresponder) : ?>
    	<li class="nodes__branch">
    	    <div class="nodes__wrap">
        		<div class="article">
        			<div class="article__body">
            			<div class="article__content">
            				<a class="text text--strong" href="edit/?id=<?=$autoresponder['id']; ?>"><?=$autoresponder['title']; ?></a>
            				<div class="text text--mute">
            				<?php
            		    	// Sender Information
            		        if ($autoresponder['from'] == 'admin')  echo Format::htmlspecialchars($super_admin['first_name'] . ' ' . $super_admin['last_name']);
            		        if ($autoresponder['from'] == 'agent')  echo __('Assigned Agent');
            		        if ($autoresponder['from'] == 'custom') echo Format::htmlspecialchars($autoresponder['from_name'] . ' &lt;' . $autoresponder['from_email'] . '&gt;');
            		        ?>
            				</div>
            			</div>
        			</div>
        			<div class="article__foot">
        			    <?=($autoresponder['active'] == 'Y') ? __('Active') : '<span class="text text--negative">' . __('Inactive') . '</span>'; ?>
                    </div>
        		</div>
    	    </div>
    	</li>
    	<?php endforeach; ?>
    </ul>
</div>
<?php else : ?>
<p class="block"><?= __('There are currently no responders.'); ?></p>
<?php endif; ?>