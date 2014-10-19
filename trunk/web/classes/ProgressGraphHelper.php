<?php

/**
 * Created by IntelliJ IDEA.
 * User: matgus
 * Date: 2014-10-19
 * Time: 9:53
 */
class ProgressGraphHelper
{
    /**
     * @param $parameters team,sprint,user as a http get parameters team=abc&sprint=xyz&user=username
     */
    public static function getProgressGraphJavaScriptCode($parameters = null)
    {
        if ($parameters == null) {
            $parameters = "";
            if (isset($_REQUEST['tester']) && strcmp($_REQUEST['tester'], '') != 0) {
                $parameters = "tester=" . $_REQUEST['tester'];
                echo '[User=' . $_REQUEST['tester'] . ']';
            }
            if (isset($_REQUEST['team']) && strcmp($_REQUEST['team'], '') != 0) {
                $parameters = $parameters . "&team=" . $_REQUEST['team'];
                echo '[Team=' . $_REQUEST['team'] . ']';

            }
            if (isset($_REQUEST['sprint']) && strcmp($_REQUEST['sprint'], '') != 0) {
                $parameters = $parameters . "&sprint=" . $_REQUEST['sprint'];
                echo '[Sprint=' . $_REQUEST['sprint'] . ']';

            }
        }
        echo "<script type='text/javascript'>

                var params = '" . $parameters . "';

$(function () {
    $.getJSON('api/statistics/progress/index.php?' + params + '&callback=?', function (data) {

        $('#container').highcharts({
            chart: {
                type: 'spline'
            },

            title: {
                text: 'Sessions executed over time'
            },
            subtitle: {
            text: 'sessions includes is in state executed, closed or debriefed'
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: { // don't display the dummy year

                    millisecond: '%H:%M:%S.%L',
                    second: '%H:%M:%S',
                    minute: '%H:%M',
                    hour: '%H:%M',
                    day: '%e. %b',
                    week: '%e. %b',
                    month: '%b \'%y',
                    year: '%Y'

                },
                title: {
                    text: 'Date'
                }
            },
            yAxis: {
                title: {
                    text: 'Sessions executed'
                },
                min: 0
            },
            tooltip: {
                headerFormat: '<b>{Sessions executed}</b><br>',
                pointFormat: '{point.x:%e. %b %Y}: {point.y:.0f} '
            },
            plotOptions: {
                series: {
                    marker: {
                        enabled: false
                    }
                }
            },
            series: [
                {
                    name: 'Total number of sessions',
                    data: data,
                    tooltip: {
                        valueDecimals: 0
                    }
                }
            ]
        });
    })
    });

	</script>";
    }
} 