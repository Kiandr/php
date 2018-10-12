<div class="fc" id="calendar" data-calendar="<?=htmlentities($calendar_app_data, ENT_QUOTES, 'UTF-8'); ?>">
<?php
// Calendar Title
?>
    <div class="menu menu--drop hidden" id="menu--filters">
        <ul class="menu__list" id="cal_quick_pick">
            <li class="menu__item"><a class="menu__link" href="?view=day&date=<?=date('Y-m-d'); ?>"><?= __('Today'); ?></a></li>
            <li class="menu__item divider"></li>
            <li class="menu__item <?=$_GET['view'] == 'default' || empty($_GET['view']) ? 'current' : ''; ?>"><a class="menu__link" href="/backend/calendar"><?= __('Calendar'); ?></a></li>
            <li class="menu__item <?=$_GET['view'] == 'day' ? 'current' : ''; ?>"><a class="menu__link" href="?view=day"><?= __('Day'); ?></a></li>
            <li class="menu__item <?=$_GET['view'] == 'list' ? 'current' : ''; ?>"><a class="menu__link" href="?view=list"><?= __('List'); ?></a></li>
        </ul>
    </div>

    <div class="bar">
        <a data-drop="#menu--filters" class="bar__title this-<?=$_GET['view'] == 'day' ? 'day' : 'month'; ?>" href="<?=sprintf($calendar_url, $calendar->currentMonth()); ?>"><?=$date; ?><svg class="icon icon-drop"><use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink"/></svg></a>
        <div class="bar__actions">
            <a class="bar__action prev-<?=($_GET['view'] == 'day' ? 'day' : 'month'); ?>" href="<?=sprintf($calendar_url, ($_GET['view'] == 'day' ? $calendar->lastDay() : $calendar->lastMonth())); ?>"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
            <a class="bar__action next-<?=($_GET['view'] == 'day' ? 'day' : 'month'); ?>" href="<?=sprintf($calendar_url, ($_GET['view'] == 'day' ? $calendar->nextDay() : $calendar->nextMonth())); ?>"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-right-a"/></svg></a>
            <a class="bar__action" href="<?=URL_BACKEND; ?>calendar/event/add/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-add"/></svg></a>
        </div>
    </div>

    <div class="block">
        <div class="input w1/1">
            <select multiple name="event_filters">
                <option><?= __('New Leads'); ?></option>
                <option><?= __('Returning Leads'); ?></option>
                <option><?= __('Form Submissions'); ?></option>
                <option><?= __('Un-categorized'); ?></option>
                <?php foreach ($options['event_types'] as $event) { ?>
                    <option class="type-<?=$event['value']; ?>"><?=$event['title']; ?></option>
                <?php } ?>
            </select>
            <?php if ($can_manage_all) { ?>
                <select name="agent" style="width: 160px; flex-grow: 0; flex-basis: 160px">
                    <option value=""><?= __('Everyone'); ?></option>
                    <option value="<?=$authuser->info('id'); ?>"><?= __('Myself'); ?></option>
                    <?php foreach ($options['agents'] as $agent) { ?>
                        <option value="<?=$agent['value']; ?>"><?=$agent['title']; ?></option>
                    <?php } ?>
                </select>
            <?php } ?>
        </div>

    </div>

<?php
// Draw Calendar View
echo $this->view->render('inc/tpl/partials/calendar/views/' . $view . '.tpl.php', [
    'days' => $days,
    'info' => $info,
    'weekday' => $info['wday'],
    'calendar' => $calendar
]);
?>

    <div id="calendar_filters">

    </div>
</div>
