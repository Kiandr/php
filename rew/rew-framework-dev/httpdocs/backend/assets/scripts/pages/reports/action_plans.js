// Dynmically import highcharts library
import(/* webpackChunkName: "vendor/highcharts" */'highcharts').then(Highcharts => {

    // Minimum date for task statistics is 1 year ago (anything older is cleaned up via cron).
    var $date = new Date();
    $date.setFullYear($date.getFullYear() - 1);

    // Date Pickers
    var $dates = $('#date_start, #date_end').datepicker({
        dateFormat: 'yy-mm-dd',
        showButtonPanel: true,
        changeMonth: true,
        changeYear: true,
        minDate: $date,
        onSelect: function (selectedDate) {
            var option = this.id == 'date_start' ? 'minDate' : 'maxDate', instance = $(this).data('datepicker');
            const date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
            $dates.not(this).datepicker('option', option, date);
        }
    });

    // Bind report load to form submit
    var $form = $('#task-report-form');
    $form.on('submit', function () {
        $('#agent-task-counts').html('<tr><td colspan="6">Loading...</td></tr>');
        loadReport($form.serialize());
        return false;
    });

    function loadReport(formData) {
        $.ajax({
            url: '?ajax',
            data: formData,
            dataType: 'json',
            success: function (json) {

                var pieData = json.pie;
                var tableHTML = json.html;

                // Initialize Pie Chart
                new Highcharts.Chart({
                    chart: {
                        renderTo: 'task-pie',
                    },
                    title: {text: 'Task Status Overview'},
                    credits: false,
                    tooltip: false,
                    spacingTop: 0,
                    marginTop: 0,
                    // Color coding for Completed, Skipped, Expired, Pending
                    colors: ['#89A54E', '#DB843D', '#AA4643', '#4572A7'],
                    plotOptions: {
                        pie: {
                            dataLabels: {
                                enabled: true,
                                color: '#000',
                                formatter: function () {
                                    return (this.key + '<br>' + this.y + ' (' + Highcharts.numberFormat(this.percentage, 1) + '%)');
                                }
                            }
                        }
                    },
                    series: [pieData]
                });

                // Load agent table
                $('#agent-task-counts').html(tableHTML);

            }
        });
    }

    // Initial load
    loadReport($form.serialize());

});
