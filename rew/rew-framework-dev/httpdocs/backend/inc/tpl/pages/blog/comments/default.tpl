<?php if (!empty($edit_comment)) : ?>

<div class="bar">
    <div class="bar__title"><?= __('Edit Blog Comment'); ?></div>
    <div class="bar__actions">
        <a class="bar__action timeline__back" href="<?='/backend/blog/comments/'; ?>"><svg class="icon icon-left-a mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
    </div>
</div>

<div class="block">

<form action="?submit" method="post" class="rew_check">
	<input type="hidden" name="edit" value="<?=htmlspecialchars($edit_comment['id']); ?>">
	<input type="hidden" name="filter" value="<?=htmlspecialchars($_GET['filter']); ?>">
	<div class="field">
		<label class="field__label"><?= __('Name'); ?> <em class="required">*</em></label>
		<?php $_POST['comment_name'] = isset($_POST['comment_name']) ? $_POST['comment_name'] : $edit_comment['name']; ?>
		<input class="w1/1" type="text" name="comment_name" value="<?=htmlspecialchars($_POST['comment_name']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Email'); ?> <em class="required">*</em></label>
		<?php $_POST['comment_email'] = isset($_POST['comment_email']) ? $_POST['comment_email'] : $edit_comment['email']; ?>
		<input class="w1/1" type="email" name="comment_email" value="<?=htmlspecialchars($_POST['comment_email']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Website'); ?></label>
		<?php $_POST['comment_website'] = isset($_POST['comment_website']) ? $_POST['comment_website'] : $edit_comment['website']; ?>
		<input class="w1/1" type="url" name="comment_website" value="<?=htmlspecialchars($_POST['comment_website']); ?>" placeholder="http://" pattern="https?://.+">
	</div>
	<div class="field">
		<label class="field__label"><?= __('Comment'); ?></label>
		<?php $_POST['comment'] = isset($_POST['comment']) ? $_POST['comment'] : $edit_comment['comment']; ?>
		<textarea class="w1/1" id="comment" name="comment" cols="24" rows="3"><?=htmlspecialchars($_POST['comment']); ?></textarea>
	</div>
	<?php $_POST['published'] = isset($_POST['published']) ? $_POST['published'] : $edit_comment['published']; ?>
	<div class="field">
		<label class="field__label"><?= __('Published'); ?></label>
		<div>
			<input id="published_true"<?=($_POST['published'] == 'true') ? ' checked="checked"' : ''; ?> type="radio" name="published" value="true">
			<label for="published_true"><?= __('Yes'); ?></label>
			<input id="published_false"<?=($_POST['published'] == 'false') ? ' checked="checked"' : ''; ?> type="radio" name="published" value="false">
			<label for="published_false"><?= __('No'); ?></label>
		</div>
	</div>
	<?php $_POST['subscribed'] = isset($_POST['subscribed']) ? $_POST['subscribed'] : $edit_comment['subscribed']; ?>
	<div class="field">
		<label class="field__label"><?= __('Subscribed'); ?></label>
		<div>
			<input id="subscribed_true"<?=($_POST['subscribed'] == 'true') ? ' checked="checked"' : ''; ?> type="radio" name="subscribed" value="true">
			<label for="subscribed_true"><?= __('Yes'); ?></label>
			<input id="subscribed_false"<?=($_POST['subscribed'] == 'false') ? ' checked="checked"' : ''; ?> type="radio" name="subscribed" value="false">
			<label for="subscribed_false"><?= __('No'); ?></label>
		</div>
	</div>
	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
		</span> </div>
</form>

</div>

<?php else : ?>

<div class="menu menu--drop hidden" id="menu--filters">
    <ul class="menu__list">
    	<li class="menu__item <?=($_GET['filter'] == 'all' || $_GET['filter'] == '')       ? ' current' : ''; ?>"><a class="menu__link" href="?filter=all"><?= __('All'); ?></a></li>
    	<li class="menu__item <?=($_GET['filter'] == 'pending')   ? ' current' : ''; ?>"><a class="menu__link" href="?filter=pending"><?= __('Pending'); ?></a></li>
    	<li class="menu__item <?=($_GET['filter'] == 'published') ? ' current' : ''; ?>"><a class="menu__link" href="?filter=published"><?= __('Published'); ?></a></li>
    	<li class="menu__item <?=($_GET['filter'] == 'published') ? ' current' : ''; ?>"><a class="menu__link" href="/backend/blog/pings/"><?= __('Ping Backs'); ?></a></li>
    </ul>
</div>

<div class="bar">
    <a href="#" data-drop="#menu--filters" class="bar__title"><?= __('Comments'); ?><?php if($_GET['filter'] != 'all') echo ', ' . Format::htmlspecialchars(ucwords($_GET['filter'])); ?> <svg class="icon icon-drop"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-drop"></use></svg></a>
    <div class="bar__actions">
		<button type="button" class="bar__action" id="btn-publish" disabled><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-out"></use></svg></button>
		<button type="button" class="bar__action" id="btn-delete" disabled><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></button>
    </div>
</div>

<form action="?submit" method="post">
	<input type="hidden" name="filter" value="<?=htmlspecialchars($_GET['filter']); ?>">
	<input type="hidden" name="action" value="">
	<?php if (!empty($comments)) : ?>

        <div class="block">
            <input type="checkbox" id="check_all">
            <label class="mar8" for="check_all">Select all</label>
        </div>

        <div class="nodes">
    		<ul class="nodes__list">
    			<?php foreach ($comments as $comment) : ?>
    			<li class="nodes__branch">
    			    <div class="nodes__wrap">

                        <div class="nodes__toggle">
        				    <input type="checkbox" name="comments[]" value="<?=$comment['id']; ?>" class="check">
                        </div>

        				<div class="article">
            				<div class="article__body">
            				    <div class="article__thumb thumb thumb--medium -bg-rew2">
                				    <svg class="icon icon--invert"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-page"></use></svg>
            				    </div>
                                <div class="article__content">
                                    <?php $article_comment = strlen($comment['comment']) > 200 ? htmlspecialchars(substr($comment['comment'], 0, 120)) . '...' : htmlspecialchars($comment['comment']); ?>
                                    <a href="/backend/blog/comments/?edit=<?=$comment['id']; ?>" title="<?=trim(strip_tags($comment['entry']['title'])); ?>" class="blog-title text text--strong"><?=$comment['entry']['title'] ?></a>
                                    <div class="text text--mute -marB8"><?=$article_comment ?></div>
                                    <?php if ($blogAuth->canManageComments($authuser)) { ?>
                                        <div><a class="text text--mute" href="<?=URL_BACKEND; ?>agents/agent/summary/?id=<?=$comment['author']['id']; ?>"><?=htmlspecialchars($comment['author']['name']); ?>, <?=Format::dateRelative($comment['date']); ?></a></div>
                                    <?php } ?>
                                </div>
            				</div>
        				</div>

        				<div class="nodes__actions">

							<?php if ($comment['published'] == 'false') : ?>
							<a class="btn btn--ico btn--ghost publish" href="<?=$comment['publishLink']; ?>" onclick="return confirm('<?= __('Are you sure you want to approve this blog comment?'); ?>');"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-out"></use></svg></a>
							<?php endif; ?>
                            <form method="post" action="<?=$comment['deleteLink']; ?>">
                                <input type="hidden" name="delete" value="<?=$comment['id']; ?>" />
                                <button onclick="return confirm('<?= __('Are you sure you would like to delete this blog comment?'); ?>');" title="<?= __('Delete'); ?>" class="btn btn--ghost btn--ico">
                                    <icon name="icon--trash--row"></icon>
                                </button>
                            </form>
        				</div>

    			    </div>
    			</li>
    			<?php endforeach; ?>
    		</ul>
        </div>
	<?php else: ?>
	<p class="block"><?= __('There are currently no %s blog comments.', ($_GET['filter'] !== 'all' ? htmlspecialchars($_GET['filter']) : '') ); ?></p>
	<?php endif; ?>

</form>
<?php endif; ?>

<?php if (!empty($paginationLinks)) { ?>
<div class="nav_pagination">
    <?php if (!empty($paginationLinks['prevLink'])) { ?>
    <a class="prev marR" href="<?=$paginationLinks['prevLink']; ?>">
        <svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg>
    </a>
    <?php } ?>
    <?php if (!empty($paginationLinks['nextLink'])) { ?>
    <a class="next" href="<?=$paginationLinks['nextLink']; ?>">
        <svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-right-a"></use></svg>
    </a>
    <?php } ?>
</div>
<?php } ?>