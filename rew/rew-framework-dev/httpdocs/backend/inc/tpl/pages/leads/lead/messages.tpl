<?php

// Render lead summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
    'title' => 'Lead Messages',
    'lead' => $lead,
    'leadAuth' => $leadAuth
]);

?>

<!-- Display selected messages -->
<?php if (!empty($myMessages)) { ?>

<div class="messages nodes">
	<ul class="nodes__list">
		<?php foreach ($myMessages as $myMessage) { ?>
		<li class="nodes__branch">
		    <div class="nodes__wrap">
    			<div class="article">
    				<div class="article__body">
        				<div class="article__content">
        					<div class="text text--strong"><?=Format::htmlspecialchars($myMessage['sent_from']); ?> to <?=Format::htmlspecialchars($myMessage['sent_to']); ?>:</div>
        					<p><?=Format::htmlspecialchars(strip_tags($myMessage['message'])); ?></p>
        					<div class="text text--mute"><time class="v" datetime="<?=date('c', strtotime($myMessage['timestamp'])); ?>" title="<?=date('l, F jS Y \@ g:ia', strtotime($myMessage['timestamp'])); ?>">
        					<?=Format::dateRelative(strtotime($myMessage['timestamp'])); ?>
        					</time></div>
        					<?php if($myMessage['user_del']=='Y') {?><div class="text text--mute"><span class="flag">DELETED</span></div><?php } ?>
        				</div>
    				</div>
    			</div>
    			<div class="nodes__actions">
    				<?php if ($myMessage['editable']) { ?>
    				<a class="btn btn--ghost edit" href="?id=<?=$lead['id']; ?>&edit=<?=$myMessage['id']; ?>&category=<?=urlencode($_GET['category']); ?>">Edit</a>
    				<?php } ?>
    				<?php if ($myMessage['reply'] == 'Y') { ?>
    				<a class="btn btn--ico btn--ghost delete" href="?id=<?=$lead['id']; ?>&delete=<?=$myMessage['id']; ?>&category=<?=urlencode($_GET['category']); ?>" onclick="return confirm('Are you sure you want to delete this message?');"><svg class="icon icon-trash mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></a>
    				<?php } ?>
    			</div>
		    </div>
		</li>
		<?php } ?>
	</ul>
</div>

	<?php if (!$thread_deleted) { ?>
		<?php if (empty($_GET['edit'])) { ?>
		<div class="block">
		    <form method="post" class="rew_check">
		    	<h2>Reply to <strong><?=$subject; ?></strong></h2>
		    	<input type="hidden" name="id" value="<?=$lead['id']; ?>">
                <input type="hidden" name="reply" value="reply">
		    	<input type="hidden" name="msg_id" value="<?=$message_id; ?>">
		    	<input type="hidden" name="category" value="<?=Format::htmlspecialchars($_GET['category']); ?>">
		    	<textarea class="tinymce email simple" name="message" rows="6" cols="85"></textarea>
		    	<br>
		    	<div class="btns">
		    		<button class="btn btn--positive" type="submit">Send</button>
		    	</div>
		    </form>
		</div>
		<?php } else { ?>
		<div class="block">
		    <form method="post" class="rew_check">
		    	<h2>Edit this Message</h2>
		    	<input type="hidden" name="id" value="<?=$lead['id']; ?>">
                <input type="hidden" name="submit" value="submit">
		    	<input type="hidden" name="edit" value="<?=$editMessage['id']; ?>">
		    	<input type="hidden" name="category" value="<?=Format::htmlspecialchars($_GET['category']); ?>">
		        <div class="field">
		            <textarea class="tinymce email simple" name="message" rows="6" cols="85"><?=Format::htmlspecialchars($editMessage['message']); ?></textarea>
		        </div>
		    	<button class="btn btn--positive" type="submit">Save</button>
		    	<a class="btn cancel" href="?id=<?=$lead['id']; ?>&category=<?=urlencode($_GET['category']); ?>">Cancel</a>
		    </form>
		</div>
		<?php } ?>
	<?php } ?>
<?php } else { ?>
	<?php if (!empty($messages)) { ?>
	<div class="nodes">
	    <ul class="nodes__list">
	    	<?php foreach($messages as $msg) { ?>
	    		<?php if ($msg['agent_read'] == 'Y' || ($msg['agent_id'] != $authuser->info('id'))) { ?>
		    	<li class="nodes__branch">
		    	    <div class="nodes__wrap">
		        		<div class="article">
		            		<div class="article__body">
		                		<div class="article__content">
		        				    <a class="text text--strong" href="?id=<?=$lead['id']; ?>&category=<?=$msg['id']; ?>"><?=$msg['subject']; ?> <?php if($msg['count'] > 1) echo '(' . $msg['count'] . ')'; ?></a>
		                            <div class="text text--mute"><time datetime="<?=date('c', strtotime($msg['latest'])); ?>" title="<?=date('l, F jS Y \@ g:ia', strtotime($msg['latest'])); ?>"><?=Format::dateRelative(strtotime($msg['latest'])); ?></time> <?=($msg['user_read'] == 'Y') ? '' : '(Unread)</strong>'; ?></div>
									<?php if($msg['user_del']=='Y') {?><div class="text text--mute"><span class="flag">DELETED</span></div><?php } ?>
		                		</div>
		        			</div>
		        		</div>
		                <div class="nodes__actions">
		        	        <a class="btn btn--ico btn--ghost" href="?id=<?=$lead['id']; ?>&delete=<?=$msg['id']; ?>" onclick="return confirm('Are you sure you want to delete this selected group of messages?');"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></a>
		    	        </div>
		    	    </div>
		    	</li>
	    		<?php } else { ?>
		    	<li class="nodes__branch">
		    	    <div class="nodes__wrap">
		        		<div class="article">
		            		<div class="article__body">
		                		<div class="article__content">
		                			<a class="text text--strong" href="?id=<?=$lead['id']; ?>&category=<?=$msg['id']; ?>">*New* <?=Format::htmlspecialchars($msg['subject']); ?></a>
		                            <div class="text text--mute">
		                			<!--<?=$msg['count']; ?>-->
		                			    <?=($msg['user_read'] == 'Y') ? '' : 'Unread'; ?>
		                                <time datetime="<?=date('c', strtotime($msg['latest'])); ?>" title="<?=date('l, F jS Y \@ g:ia', strtotime($msg['latest'])); ?>"><?=Format::dateRelative(strtotime($msg['latest'])); ?></time>
										<?php if($msg['user_del']=='Y') {?><div class="text text--mute"><span class="flag">DELETED</span></div><?php } ?>
		                            </div>
		                		</div>
		            		</div>
		        		</div>
		        		<div class="nodes__actions">
		                    <a class="btn btn--ico btn--ghost" href="?id=<?=$lead['id']; ?>&delete=<?=$msg['id']; ?>" onclick="return confirm('Are you sure you want to delete this selected group of messages?');"><svg class="icon icon-trash mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></a>
		        		</div>
		    	    </div>
		    	</li>
	    		<?php } ?>
	    	<?php } ?>
	    </ul>
	</div>
	<?php } ?>


<div class="block">

	<form method="post" class="rew_check">
		<input type="hidden" name="id" value="<?=$lead['id']; ?>">
        <input type="hidden" name="submit" value="submit">

		<h3>Send New Message</h3>

		<div class="field">
			<input class="w1/1" type="text" name="subject" value="<?=htmlspecialchars($_POST['subject']); ?>" placeholder="Subject" required>
		</div>

		<div class="field">
			<textarea class="tinymce email simple" name="message" rows="6" cols="85"><?=htmlspecialchars($_POST['message']); ?></textarea>
		</div>

		<div class="btns btns--stickyB">
			<span class="R">
				<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> Send</button>
			</span>
		</div>

		</div>
	</form>

</div>

<?php } ?>
