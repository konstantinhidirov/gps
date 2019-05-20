<?php

namespace app\commands;

use app\lib\Geo;
use yii\console\Controller;

class TestController extends Controller
{
    public function actionDistance()
    {
        //var_export(Geo::getDistanceM(46.329141, 48.047435, 0, 46.330814, 48.048287, 0));
        var_export(Geo::getLatLngBetween(46.329141, 48.047435, 1, 46.330814, 48.048287, 1, 0.5));
    }
}