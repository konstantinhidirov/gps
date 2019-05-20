<?php

namespace app\lib;

class Geo
{
    public static function llaToXYZm($lat, $long, $altM)
    {
        $ecef = (new \Geodetic\LatLong(
            new \Geodetic\LatLong\CoordinateValues(
                $lat, $long, \Geodetic\Angle::DEGREES,
                $altM, \Geodetic\Distance::METRES
            )
        ))->toECEF(new \Geodetic\Datum(\Geodetic\Datum::WGS84));

        return [
            $ecef->getX()->getValue(\Geodetic\Distance::METRES),
            $ecef->getY()->getValue(\Geodetic\Distance::METRES),
            $ecef->getZ()->getValue(\Geodetic\Distance::METRES)
        ];
    }

    public static function coordInRatio($a1, $a2, $ratio)
    {
        return ($a1 + $ratio * $a2) / (1 + $ratio);
    }

    /**
     * --------TS1---------targetTS-------------------TS2-------> t
     */
    public static function timeRatio($targetTS, $ts1, $ts2)
    {
        return ($targetTS - $ts1) / ($ts2 - $targetTS);
    }

    /**
     * Координаты LAT/LNG/ALT точки между двумя другими на определенном проценте пройденного пути между ними
     */
    public static function getLatLngBetween($lat1, $lng1, $alt1, $lat2, $lng2, $alt2, $ratio)
    {
        list($x1, $y1, $z1) = self::llaToXYZm($lat1, $lng1, $alt1);
        list($x2, $y2, $z2) = self::llaToXYZm($lat2, $lng2, $alt2);

        $ecef = new \Geodetic\ECEF(
            new \Geodetic\ECEF\CoordinateValues(
                self::coordInRatio($x1, $x2, $ratio),
                self::coordInRatio($y1, $y2, $ratio),
                self::coordInRatio($z1, $z2, $ratio),
                \Geodetic\Distance::METRES
            )
        );

        $latLong = $ecef->toLatLong(new \Geodetic\Datum(\Geodetic\Datum::WGS84));
        return [
            $latLong->getLatitude()->getValue(\Geodetic\Angle::DEGREES),
            $latLong->getLongitude()->getValue(\Geodetic\Angle::DEGREES),
            $latLong->getHeight()->getValue(\Geodetic\Distance::METRES)
        ];
    }

    public static function getDistanceM($lat1, $lng1, $alt1, $lat2, $lng2, $alt2)
    {
        $lla1 = new \Geodetic\LatLong(
            new \Geodetic\LatLong\CoordinateValues(
                $lat1, $lng1, \Geodetic\Angle::DEGREES,
                $alt1, \Geodetic\Distance::METRES
            )
        );

        $lla2 = new \Geodetic\LatLong(
            new \Geodetic\LatLong\CoordinateValues(
                $lat2, $lng2, \Geodetic\Angle::DEGREES,
                $alt2, \Geodetic\Distance::METRES
            )
        );

        return $lla1->getDistanceVincenty($lla2, new \Geodetic\ReferenceEllipsoid(
                \Geodetic\ReferenceEllipsoid::WGS_84)
        )->getValue(\Geodetic\Distance::METRES);

    }
}