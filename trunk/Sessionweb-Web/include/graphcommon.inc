<?php
function getPieCharTimeDistribution($allSessions, $divId, $title = "Time distribution")
{
    $setup = 0;
    $test = 0;
    $bug = 0;
    $opp = 0;
    $duration = 0;

    foreach ($allSessions as $sessionid => $sessionObject)
    {
        $setup = $setup + $sessionObject['setup_percent'];
        $test = $test + $sessionObject['test_percent'];
        $bug = $bug + $sessionObject['bug_percent'];
        $opp = $opp + $sessionObject['opportunity_percent'];
        $duration = $duration + $sessionObject['duration_time'];
    }
    if ($allSessions != 0 && $setup != 0)
        $setup = $setup / count($allSessions);
    if ($allSessions != 0 && $test != 0)
        $test = $test / count($allSessions);
    if ($allSessions != 0 && $bug != 0)
        $bug = $bug / count($allSessions);
    if ($allSessions != 0 && $opp != 0)
        $opp = $opp / count($allSessions);

    $setupTime = round($setup * $duration / 100, 2);
    $testTime = round($test * $duration / 100, 2);
    $bugTime = round($bug * $duration / 100, 2);
    $oppTime = round($opp * $duration / 100, 2);

    $htmlString = '
<script type="text/javascript">
    var chart;
    $(document).ready(function () {
        chart = new Highcharts.Chart({
            chart:{
                renderTo:\'' . $divId . '\',
                plotBackgroundColor:null,
                plotBorderWidth:null,
                plotShadow:false
            },
            title:{
                text:\'' . $title . '\'
            },
            tooltip:{
                formatter:function () {
                    return \'<b>\' + this.point.name + \'</b>: \' + this.percentage.toFixed(1) + \' %\';
                }
            },
            plotOptions:{
                pie:{
                    allowPointSelect:true,
                    cursor:\'pointer\',
                    dataLabels:{
                        enabled:true,
                        color:\'#000000\',
                        connectorColor:\'#000000\',
                        formatter:function () {
                            return \'<b>\' + this.point.name + \'</b>: \' + this.percentage.toFixed(1) + \' %\';
                        }
                    }
                }
            },
            series:[
                    {
                        type:\'pie\',
                        name:\'Browser share\',
                        data:
                        [{
                        name: \'Setup (' . $setupTime . 'h)\',
                        y:  ' . $setup . ',
                        color: \'#0000FF\'
                    }, {
                        name: \'Test (' . $testTime . 'h)\',
                        y: ' . $test . ',
                        color: \'#00FF00\'
                    }, {
                        name: \'Bug (' . $bugTime . 'h)\',
                        y: ' . $bug . ',
                        color: \'#FF0000\'
                    }, {
                        name: \'opportunity (' . $oppTime . 'h)\',
                        y: ' . $opp . ',
                        color: \'#000000\'
                    }
                ]
                }
            ]
        });
    });

</script>';
    return $htmlString;

}