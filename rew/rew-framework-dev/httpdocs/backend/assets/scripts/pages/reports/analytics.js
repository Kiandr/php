// Dynmically import highcharts library
import(/* webpackChunkName: "vendor/highcharts" */'highcharts').then(Highcharts => {

    // GA Containers
    var $gload = $('#ga-load'), $gdata = $('#ga-data'), $loader = $('#ga-loader'), notify;

    // Data Collections
    var collections = [];

    // Load GA Data
    $gload.bind('click', function () {

        // Require Profile
        if ($('#ga_profile').length < 1) return false;

        // Reset Data
        collections = [];

        // Show Data
        $gdata.removeClass('hidden');

        // GA Data
        var types = ['usage', 'visitors', 'referers', 'keywords', 'sources'];

        for (var t in types) {

            // Data Type
            var type = types[t];

            // Data Container
            var $container = $('#ga-' + type);

            // Reset Table / Chart
            $container.find('.table, .chart').html('').addClass('hidden');

            // Add Loader
            var $load = $container.find('.loader');
            if ($load.length === 0) $load = $loader.clone().removeClass('hidden').addClass('loader').appendTo($container);

            // Hide Site Usage Stats
            if (type == 'usage') {
                $('#leads_dashboard_summary').addClass('hidden');
            }

            // AJAX Request (JSON Response)
            $.ajax({
                cache    : false,
                url      : '?load',
                type     : 'POST',
                dataType : 'json',
                data     : {
                    ajax : true,
                    type : type,
                    ga_profile : $('#ga_profile').val(),
                    ga_segment : $('#ga_segment').val(),
                    date_start : $('#date_start').val(),
                    date_end   : $('#date_end').val()
                },
                success  : function (json, textStatus, jqXHR) { // eslint-disable-line no-unused-vars

                    // Data Collection
                    var collection = {
                        type : json.type
                    };

                    // Data Table
                    if (json.table) {
                        collection.table = json.table;
                    }

                    // Data Chart
                    if (json.chart) {
                        collection.chart = json.chart;
                    }

                    // Store Data Collection
                    collections[collections.length] = collection;

                    // Remove Loader
                    $('#ga-' + json.type).find('.loader').remove();

                    // Visual Data
                    visualizeData(collections.length - 1);

                    // Error Notifications
                    if (json.errors) {
                        if (notify) notify.close();
                        notify = $('#notifications').notify('create', 'notify-error', {
                            title : 'An Error Has Occurred!',
                            text: '<ul><li>' +  json.errors.join('</li><li>') + '</li></ul>'
                        });
                    }

                },
                error : function (jqXHR, textStatus, errorThrown) { // eslint-disable-line no-unused-vars

                    // Error Notifications
                    if (notify) notify.close();
                    notify = $('#notifications').notify('create', 'notify-error', {
                        title : 'An Error Has Occurred!',
                        text: '<ul><li>Your request could not be completed.</li></ul>'
                    });

                }
            });

        }

    }).trigger('click');

    // Draw Data Tables
    var visualizeData = function (index) {
        var l = collections.length;
        if (l > 0) {
            var i = index || 0;
            for (i; i < l; i++) {
                var collection = collections[i];

                // Data Container
                var $container = $('#ga-' + collection.type);

                // Site Usage Stats.
                if (collection.type == 'usage') {
                    $('#leads_dashboard_summary').removeClass('hidden');
                    $.each(collection.table, function (name, value) {
                        $('#ga-' + name).find('.count').html(value);
                    });
                    return;
                }

                // Data Table
                var table = $container.find('.table').removeClass('hidden');
                if (table && collection.table) {
                    table.html(collection.table);
                }

                // Data Chart
                var chart = $container.find('.chart').removeClass('hidden').get(0);
                if (chart && collection.chart) {

                    // Create Chart
                    new Highcharts.Chart({
                        chart: {
                            renderTo: chart,
                        },
                        title : false,
                        credits : false,
                        spacingTop: 0,
                        marginTop: 0,
                        colors: ['#0077cc','#019700','#9966cc','#ff6600','#dbe8f5'],
                        xAxis: {
                            type: 'datetime',
                            dateTimeLabelFormats : {
                                day : '%b %e'
                            },
                            title: {
                                text: null
                            }
                        },
                        yAxis: {
                            title : false,
                            min : 0,
                            startOnTick: false,
                            showFirstLabel: false,
                            gridLineColor: '#eee'
                        },
                        tooltip: {
                            shared: true,
                            crosshairs: true,
                            formatter: function() {
                                var s = '<strong>' + Highcharts.dateFormat('%A, %B %e, %Y', this.x) + '</strong><br/>';
                                $.each(this.points, function(i, point) {
                                    s += '<strong style="font-weight: bold; color: ' + point.series.color + ';">' + point.series.name + ':</strong> ' + Highcharts.numberFormat(point.y, 0) + '<br>';
                                });
                                return s;
                            }
                        },
                        plotOptions: {
                            line: {
                                lineWidth: 5,
                                marker: {
                                    radius: 5,
                                    symbol: 'circle',
                                    fillColor: null,
                                    lineWidth: 1,
                                    lineColor: '#FFFFFF',
                                    enabled: true,
                                    states: {
                                        hover: {
                                            enabled: true,
                                            radius: 8
                                        }
                                    }
                                },
                                shadow: false,
                                states: {
                                }
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
                        series: collection.chart.series
                    });

                }

            }
        }
    };

    // Redraw Charts on Window Resize
    //$(window).bind('resize', function (event) {
    //	visualizeData();
    //});

    // Date Range Pickers
    var search_dates = $('#date_start, #date_end').datepicker({
        dateFormat: 'yy-mm-dd',
        showButtonPanel: true,
        changeMonth: true,
        changeYear: true,
        minDate : new Date(2005, 0, 1),
        onSelect: function (selectedDate) {
            var option = this.id == 'date_start' ? 'minDate' : 'maxDate';
            var instance = $(this).data('datepicker');
            var date = $.datepicker.parseDate(
                instance.settings.dateFormat || $.datepicker._defaults.dateFormat,
                selectedDate,
                instance.settings);
            search_dates.not(this).datepicker('option', option, date);
        }
    });

});
