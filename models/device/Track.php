<?php

namespace app\models\device;

use app\lib\Geo;
use app\models\device\track\Pair;
use yii\db\ActiveRecord;

class Track extends ActiveRecord
{

    public static function tableName()
    {
        return 'tracks';
    }

    public function processPairs()
    {
        $existingPairs = Pair::find()->where([
            'or',
            ['id1' => $this->id],
            ['id2' => $this->id]
        ])->all();
        $excludeTracks = [];
        foreach ($existingPairs as $pair)
            $excludeTracks[] = ($pair->id1 == $this->id) ? $pair->id2 : $pair->id1;
        $tracks = Track::find()->select(['id'])
            ->where([
                'and',
                'end_at is not null',
                [
                    'not',
                    ['id' => $excludeTracks]
                ],
                [
                    'or',
                    ['between', 'begin_at', $this->begin_at, $this->end_at],
                    ['between', 'end_at', $this->begin_at, $this->end_at],
                    [
                        'and',
                        ['<=', 'begin_at', $this->begin_at],
                        ['>=', 'end_at', $this->end_at],
                    ]
                ]
            ])
            ->column();
        $currentLocations = [];
        foreach (Location::find()
                     ->where(['track_id' => $this->id])
                     ->orderBy(['ts' => SORT_ASC])
                     ->all() as $location)
            $currentLocations[$location->ts] = $location->attributes;

        $commTracks = [];
        foreach ($tracks as $track) {
            $locations = [];
            $time = 0;
            foreach (Location::find()
                         ->where(['track_id' => $track])
                         ->orderBy(['ts' => SORT_ASC])
                         ->all() as $location) {
                $locations[$location->ts] = $location->attributes;
                foreach ($currentLocations as $currentTS => $currentLocation) {
                    $tsPrev = 0;
                    foreach (array_keys($locations) as $ts) {
                        if (!$tsPrev)
                            $tsPrev = $ts;
                        if ($ts >= $currentTS)
                            break;
                        $tsPrev = $ts;
                    }
                    $hisCurrentLocation = Geo::getLatLngBetween(
                        $locations[$tsPrev]['lat'], $locations[$tsPrev]['lon'], $locations[$tsPrev]['alt'],
                        $locations[$ts]['lat'], $locations[$ts]['lon'], $locations[$ts]['alt'],
                        Geo::timeRatio($currentTS, $tsPrev, $ts)
                    );
                    $distance = Geo::getDistanceM(
                        $locations[$currentTS]['lat'], $locations[$currentTS]['lon'], $locations[$currentTS]['alt'],
                        $hisCurrentLocation[0], $hisCurrentLocation[1], $hisCurrentLocation[2]
                    );
                    if ($distance <= Pair::MAX_DISTANCE) {
                        $time += $ts - $tsPrev;
                        if ($time >= Pair::MIN_TIME) {
                            $commTracks[] = $track;
                            break 2;
                        }
                    }
                    foreach (array_keys($locations) as $ts)
                        if ($ts < $tsPrev) {
                            unset($locations[$ts]);
                        } else
                            break;
                }
            }
        }
        foreach ($commTracks as $track) {
            ($pair = new Pair())->setAttributes(['id1' => $this->id, 'id2' => $track], false);
            $pair->save(false);
        }
    }
}
