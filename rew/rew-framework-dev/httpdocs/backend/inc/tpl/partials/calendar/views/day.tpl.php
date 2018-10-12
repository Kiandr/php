
<table id="calendar_day">

    <tbody>

        <tr>

            <th class="calendar__heading"><?= __('All Day'); ?></th>
            <td class="slot" id="all_day"></td>

        </tr>
        <tr class="separator"><th></th><td></td></tr>

        <?php // for each hour in the day ?>
        <?php for ($i = 0; $i < REW\Backend\Calendar::HOURS_IN_A_DAY; $i++) { ?>
            <tr class="half">

                <th class="calendar__heading"><?=date('ga', strtotime($i . ':00:00')); ?></th>
                <td class="slot" id="<?=date('g:i-a', strtotime($i . ':00:00')); ?>"></td>

            </tr>
            <tr>
                <th></th>
                <td class="slot" id="<?=date('g:i-a', strtotime($i . ':30:00')); ?>"></td></td>
            </tr>
        <?php } ?>

    </tbody>
</table>
