<?php

namespace app\models;

use app\models\device\Activity;
use app\models\device\Cache;
use app\models\device\Location;
use app\models\device\Status;
use app\models\device\Track;
use yii\db\ActiveRecord;
use yii\db\Expression;

class Device extends ActiveRecord implements \yii\web\IdentityInterface
{

    public static function tableName()
    {
        return 'devices';
    }

    public function getNotificationChannel()
    {
        return 'device' . $this->id;
    }

    public function getCache()
    {
        return $this->hasOne(Cache::class, ['device_id' => 'id']);
    }

    public function getTrack()
    {
        return $this->hasOne(Track::class, ['device_id' => 'id'])
            ->orderBy(['begin_at' => SORT_DESC]);
    }

    public function setStatus($ts, $type)
    {
        $statusData = [
            'device_id' => $this->id,
            'ts' => $ts
        ];
        if (Status::find()->where($statusData)->count())
            return;

        $statusData['type'] = $type;
        ($status = new Status())
            ->setAttributes($statusData, false);
        $status->save(false);

        if ($ts >= $this->cache->status_ts) {
            $cacheData = ['status_ts' => $ts];
            if ($this->cache->status_type != $type) {
                $cacheData['status_type'] = $type;
                if ($type == Status::STATUS_WALK) {
                    ($track = (new Track()))->setAttributes([
                        'device_id' => $this->id,
                        'begin_at' => $ts
                    ], false);
                    $track->save(false);
                    unset($this->track);
                    $cacheData['track_id'] = $this->track->id;
                } elseif ($this->cache->status_type == Status::STATUS_WALK)
                    if ($this->track)
                        $this->track->updateAttributes(['end_at' => $ts]);
            }
            $this->cache->updateAttributes($cacheData);
            unset($this->cache);
        }
    }

    public function setLocation($ts, $lat, $lon, $alt, $accu)
    {
        $locData = [
            'device_id' => $this->id,
            'ts' => $ts
        ];
        if (Location::find()->where($locData)->count())
            return;

        ($location = (new Location()))->setAttributes(
            array_merge($locData, [
                'lat' => $lat,
                'lon' => $lon,
                'alt' => $alt,
                'track_id' => $this->cache->track_id
            ]), false);
        $location->save(false);

        if ($ts >= $this->cache->status_ts) {
            $this->cache->updateAttributes([
                'status_ts' => $ts,
                'location_lat' => $lat,
                'location_lon' => $lon,
                'location_alt' => $alt,
                'location_accu' => $accu,
                'position' => new Expression('ST_GeomFromText(\'POINT(' . $lat . ' ' . $lon . ')\', 4326)')
            ]);
            unset($this->cache);
        }
    }

    public function setActivity($ts, $type, $steps)
    {
        $actData = [
            'device_id' => $this->id,
            'ts' => $ts
        ];
        if (Activity::find()->where($actData)->count())
            return;

        ($activity = (new Activity()))->setAttributes(
            array_merge($actData, [
                'type' => $type,
                'steps' => $steps,
                'track_id' => ($this->cache->status_type == Status::STATUS_WALK) ? $this->cache->track_id : null
            ]), false);
        $activity->save(false);
    }

    public static function getInPolygon(array $poly)
    {
        return self::find()->where(['id' => Cache::getInPolygon($poly)])->all();
    }

    /* IdentityInterface */

    public function getId()
    { //ii
        return $this->id;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    { //ii
        if (!$token)
            return null;
        $user = static::find()->where(['token' => $token])->one();
        return $user;
    }

    public static function findIdentity($id)
    { //ii
        return null;
    }

    public function getAuthKey()
    { //ii
        return null;
    }

    public function validateAuthKey($authKey)
    { //ii
        return false;
    }
}
