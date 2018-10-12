// MLS listing autocomplete
import 'common/mls-autocomplete';

// Dynmically import highcharts library
import(/* webpackChunkName: "vendor/highcharts" */'highcharts').then(Highcharts => {

    // Quick Filters
    var $filters = $('#select-filter').on('change', function () {
        var $this = $(this), val = $this.val();
        if (val === 'all') {
            $dates.prop('disabled', true).prop('required', false).val('');
        } else {
            $dates.prop('required', true).prop('disabled', false);
            if (val === 'custom') {
                $dates.val('');
            } else {
                var range = val.split('|');
                $('#date_start').datepicker('setDate', range[0]);
                $('#date_end').datepicker('setDate', range[1]);
            }
        }
    });

    // Date Pickers
    var $dates = $('#date_start, #date_end').datepicker({
        dateFormat: 'yy-mm-dd',
        showButtonPanel: true,
        changeMonth: true,
        changeYear: true,
        minDate: new Date(2005, 0, 1),
        onSelect: function (selectedDate) {
            const option = this.id == 'date_start' ? 'minDate' : 'maxDate', instance = $(this).data('datepicker');
            const date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
            $dates.not(this).datepicker('option', option, date);
            $filters.val('custom');
        }
    });

    // Submit Form
    const $btn = $('#btn-update');
    const btnText = $btn.text();
    const $form = $btn.closest('form').on('submit', function () {
        const $form = $(this).addClass('loading');
        const data = $form.serialize();
        $('#listing-report').html('');
        $btn.removeClass('positive').text('Loading...').prop('disabled', true);
        window.location.hash = 'start=' + $form.find('input[name="start"]').val() + '&end=' + $form.find('input[name="end"]').val();
        $.ajax({
            url: '?ajax',
            data: data,
            dataType: 'json',
            success: function (report) {
                $btn.addClass('positive').text(btnText).prop('disabled', false);
                $form.removeClass('loading');
                loadReport(report);
            }
        });
        return false;
    });

    // Parse the Hash & Detect Changes (Doesn't work in IE8)
    var currHash = window.location.hash.replace('#', '');
    $(window).on('hashchange', function () {
        var hash = window.location.hash.replace('#', '');
        if (hash.length > 0) {
            // Hash Data
            var arr = hash.split('&'), i = 0, l = arr.length, values = [], data = {};
            for (i; i < l; i++) {
                values = arr[i].split('=');
                data[values[0]] = values[1];
            }
            // Start & End Date
            if (data.start && data.end) {
                var opt = 'option[value="' + data.start + '|' + data.end + '"]';

                if ($filters.find(opt).length > 0) {
                    $filters.val(data.start + '|' + data.end).trigger('change');
                } else {
                    $filters.val('custom').trigger('change');
                    $('input[name="start"]').val(data.start);
                    $('input[name="end"]').val(data.end);

                }
            }
            // Submit Form
            $form.not('.loading').trigger('submit');
            // No Hash
        } else {
            // No Report
            $('#listing-report').html('');
        }
    }).trigger('hashchange');

    // Submit Form on First Load
    if (currHash.length === 0) $form.trigger('submit');
    $('#listing-filters').removeClass('hidden');

    // Load Report Data
    var loadReport = function (data) {

        // Require Data
        if (!data || !data.html) return;

        // Load HTML
        $('#listing-report').html(data.html);

        // Activity Chart
        if (data.series) {
            new Highcharts.Chart({
                chart: {
                    renderTo: 'activity-chart',
                },
                title: false,
                subtitle: {
                    text: (function () {
                        var start = $('#date_start').datepicker('getDate'), end = $('#date_end').datepicker('getDate');
                        if (!start || !end) {
                            start = data.minDate;
                            end = data.maxDate;
                            if (!start || !end) {
                                return '';
                            }
                        }
                        return Highcharts.dateFormat('%A, %B %e, %Y', start) + ' to ' + Highcharts.dateFormat('%A, %B %e, %Y', end);
                    })()
                },
                credits: false,
                spacingTop: 0,
                marginTop: 0,
                xAxis: {
                    type: 'datetime',
                    title: {
                        text: null
                    },
                    gridLineColor: '#E7E7E7',
                    labels: {
                        style: {
                            color: '#268CCD',
                            fontSize: '10px'
                        }
                    },
                    minTickInterval: 24 * 3600 * 1000,
                    dateTimeLabelFormats: {
                        day: '%b %e',
                        month: '%B %Y',
                        year: '%Y'
                    }
                },
                yAxis: {
                    min: 1,
                    title: false,
                    startOnTick: false,
                    showFirstLabel: false,
                    allowDecimals: false,
                    gridLineColor: '#EEE',
                    labels: {
                        x: 15,
                        y: 15,
                        style: {
                            color: '#999',
                            fontSize: '10px'
                        }
                    }
                },
                plotOptions: {
                    area: {
                        lineWidth: 4,
                        fillOpacity: 0.25,
                        marker: {
                            radius: 5,
                            symbol: 'circle',
                            fillColor: null,
                            lineWidth: 3,
                            lineColor: '#FFFFFF',
                            enabled: true,
                            states: {
                                hover: {
                                    enabled: true,
                                    radius: 8
                                }
                            }
                        },
                        shadow: false
                    }
                },
                legend: {
                    borderWidth: 0,
                    itemStyle: {
                        color: '#666'
                    },
                    itemHoverStyle: {
                        color: '#333'
                    }
                },
                tooltip: {
                    shared: true,
                    formatter: function () {


                        // Get UTC Date
                        var date = new Date(this.x);
                        date = new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds());

                        // Tooltip Title
                        var title = Highcharts.dateFormat('%A, %B %e, %Y', date);

                        // Generate Tooltip HTML
                        var s = '<strong>' + title + '</strong><br/>', total = 0;
                        $.each(this.points, function (i, point) {
                            total += point.y;
                            if (point.y > 0) s += '<strong style="font-weight: bold; color: ' + point.series.color + ';">' + point.series.name + ':</strong> ' + Highcharts.numberFormat(point.y, 0) + '<br>';
                        });

                        // Return Tooltip (If point has value)
                        return (total === 0 ? false : s);

                    }
                },
                series: data.series
            });
        }

        // Pie Chart
        if (data.pie) {
            new Highcharts.Chart({
                chart: {
                    renderTo: 'activity-pie',
                },
                title: false,
                credits: false,
                spacingTop: 0,
                marginTop: 0,
                plotOptions: {
                    pie: {
                        showInLegend: false,
                        dataLabels: {
                            enabled: true,
                            color: '#FFF',
                            distance: -30,
                            formatter: function () {
                                return (this.percentage > 5 ? Highcharts.numberFormat(this.percentage, 0) + '%' : '');
                            }
                        }
                    }
                },
                series: [data.pie]
            }, function (pie) {

                // Show Tooltip for First Point
                pie.tooltip.refresh(pie.series[0].data[0]);

            });
        }

    };

});
