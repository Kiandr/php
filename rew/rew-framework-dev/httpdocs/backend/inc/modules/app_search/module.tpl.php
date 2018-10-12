<?php if ($this->config('ajax') == true) : ?>

    <?php global $authuser; ?>

    <?php if (!empty($errors)) : ?>
        <div class="rewui message negative">
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li><?=$error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (empty($total)) : ?>

        <p><em><?= __('No Matches Found'); ?></em></p>

    <?php else : ?>

        <?php if (!empty($results['sections'])) : ?>
            <strong>Sections (<?=number_format(count($results['sections'])); ?>)</strong>
            <ul>
                <?php foreach ($results['sections'] as $k => $result) : ?>
                    <?php if ($k == 10) : ?>
                        <li class="indent"><?= __('%s More ...', number_format(count($results['sections'])-10)); ?></li>
                        <?php break; endif; ?>
                    <li><a href="<?=$result['href'];?>"><?=preg_replace('/(' . preg_quote(htmlspecialchars($_GET['q'])) . ')/i', '<span class="match">$1</span>', htmlspecialchars($result['title'])); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>


        <?php if (!empty($results['files'])) : ?>
            <strong><?= __('File Manager (%s)', number_format($results['files_cnt'])); ?></strong>
            <ul>
                <?php foreach ($results['files'] as $k => $result) : ?>
                    <?php if ($k == 10) : ?>
                        <li class="indent"><?= __('%s More ...', number_format($results['files_cnt']-10)); ?></li>
                        <?php break; endif; ?>
                    <li><a href="<?=Settings::getInstance()->URLS['URL'] . 'files/' . $result['id'] . '/' . $result['name']; ?>" target="_blank"><?=preg_replace('/(' . preg_quote(htmlspecialchars($_GET['q'])) . ')/i', '<span class="match">$1</span>', htmlspecialchars($result['name'])); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($results['pages'])) : ?>
            <strong><?= __('CMS Pages (%s)', number_format($results['pages_cnt'])); ?></strong>
            <ul>
                <?php foreach ($results['pages'] as $k => $result) : ?>
                    <?php if ($k == 10) : ?>
                        <li class="indent"><?= __('%s More ...', number_format($results['pages_cnt']-10)); ?></li>
                        <?php break; endif; ?>
                    <li><a href="<?=URL_BACKEND; ?>cms/pages/edit/?id=<?=$result['page_id']; ?>"><?=preg_replace('/(' . preg_quote(htmlspecialchars($_GET['q']),'/') . ')/i', '<span class="match">$1</span>', htmlspecialchars($result['link_name'])); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($results['snippets'])) : ?>
            <strong><?= __('Snippets (%s)', number_format($results['snippets_cnt'])); ?></strong>
            <ul>
                <?php foreach ($results['snippets'] as $k => $result) : ?>
                    <?php if ($k == 10) : ?>
                        <li class="indent"><?= __('%s More ...', number_format($results['snippets_cnt']-10)); ?></li>
                        <?php break; endif; ?>
                    <li><a href="<?=URL_BACKEND; ?>cms/snippets/edit/?id=<?=$result['name']; ?>">#<?=preg_replace('/(' . preg_quote(htmlspecialchars($_GET['q'])) . ')/i', '<span class="match">$1</span>', htmlspecialchars($result['name'])); ?>#</a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($results['blog'])) : ?>
            <strong><?= __('Blog Entries (%s)', number_format($results['blog_cnt'])); ?></strong>
            <ul>
                <?php foreach ($results['blog'] as $k => $result) : ?>
                    <?php if ($k == 10) : ?>
                        <li class="indent"><?= __('%s More ...', number_format($results['blog_cnt']-10)); ?></li>
                        <?php break; endif; ?>
                    <li><a href="<?=URL_BACKEND; ?>blog/entries/edit/?id=<?=$result['id']; ?>"><?=preg_replace('/(' . preg_quote(htmlspecialchars($_GET['q'])) . ')/i', '<span class="match">$1</span>', htmlspecialchars($result['title'])); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($results['directory'])) : ?>
            <strong><?= __('Directory Listings (%s)', number_format($results['directory_cnt'])); ?></strong>
            <ul>
                <?php foreach ($results['directory'] as $k => $result) : ?>
                    <?php if ($k == 10) : ?>
                        <li class="indent"><?= __('%s More ...', number_format($results['directory_cnt']-10)); ?></li>
                        <?php break; endif; ?>
                    <li><a href="<?=URL_BACKEND; ?>directory/listings/edit/?id=<?=$result['id']; ?>"><?=preg_replace('/(' . preg_quote(htmlspecialchars($_GET['q'])) . ')/i', '<span class="match">$1</span>', htmlspecialchars($result['business_name'])); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($results['agents'])) : ?>
            <strong><?= __('Agents (%s)', number_format($results['agents_cnt'])); ?></strong>
            <ul>
                <?php foreach ($results['agents'] as $k => $result) : ?>
                    <?php if ($k == 10) : ?>
                        <li class="indent"><?= __('%s More ...', number_format($results['agents_cnt']-10)); ?></li>
                        <?php break; endif; ?>
                    <li><a href="<?=URL_BACKEND; ?>agents/agent/summary/?id=<?=$result['id']; ?>"><?=preg_replace('/(' . preg_quote(htmlspecialchars($_GET['q'])) . ')/i', '<span class="match">$1</span>', htmlspecialchars($result['first_name'] . ' ' . $result['last_name'])); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($results['leads'])) : ?>
            <strong><?= __('Leads (%s)', number_format($results['leads_cnt'])); ?></strong>
            <ul>
                <?php foreach ($results['leads'] as $k => $result) : ?>
                    <?php if ($k == 10) : ?>
                        <li class="indent"><?= __('%s More ...', number_format($results['leads_cnt']-10)); ?></li>
                        <?php break; endif; ?>
                    <?php
                    $name = Format::trim($result['first_name'].' '.$result['last_name']);
                    $name = $name ?: $result['email'];
                    ?>
                    <?php if ($authuser->isLender()) { ?>
                        <li><a href="<?=URL_BACKEND; ?>leads/search/?submit=true&email=<?=urlencode($result['email']); ?>"><?=preg_replace('/(' . preg_quote(htmlspecialchars($_GET['q'])) . ')/i', '<span class="match">$1</span>', htmlspecialchars($name)); ?></a></li>
                    <?php } else { ?>
                        <li><a href="<?=URL_BACKEND; ?>leads/lead/summary/?id=<?=$result['id']; ?>"><?=preg_replace('/(' . preg_quote(htmlspecialchars($_GET['q'])) . ')/i', '<span class="match">$1</span>', htmlspecialchars($name)); ?></a></li>
                    <?php } ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    <?php endif; ?>
<?php endif; ?>
