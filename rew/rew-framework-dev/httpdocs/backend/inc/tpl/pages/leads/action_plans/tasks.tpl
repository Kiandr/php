<?php if (empty($authorized)) { ?>

    <?php

    echo errorMsg(
        __('You do not have permission to access this page.'),
        __('%s Authorization Error', '<img src="/backend/img/ills/security.png" width=200/>')
    );

    ?>

<?php } else { ?>

<section>

	<header>
		<h1><?= __('Admin Task List'); ?></h1>
		<div class="app_actions"></div>
	</header>

	<?php $page->container('task-list')->module($task_list)->display(); ?>

</section>

<?php } ?>