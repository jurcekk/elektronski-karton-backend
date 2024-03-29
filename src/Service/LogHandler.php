<?php

namespace App\Service;

use App\Entity\Log;
use MobileDetectBundle\DeviceDetector\MobileDetectorInterface;

class LogHandler
{

    public function getMyLoginLocation(MobileDetectorInterface $detector):Log
    {
        $deviceDetect = new MobileDetectRepository($detector);
//
        $ip = getenv("HTTP_X_FORWARDED_FOR");
        $export = (object)(unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip)));
        $newLog = (object)[
            'device'=>$deviceDetect->getDeviceInfo(),
            'country'=>$export->geoplugin_countryName,
            'ip'=>$export->geoplugin_request
        ];
        //this method will probably be rewritten to geocoder object...
        return new Log($newLog->device,$newLog->country,$newLog->ip);
    }

//    public function getMyCoordinates(MobileDetectorInterface $detector):object
//    {
//        $deviceDetect = new MobileDetectRepository($detector);
//
//        $ip = getenv("HTTP_X_FORWARDED_FOR");
//        $export = (object)(unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip)));
//
//        $coordinates = (object)[
//            'latitude'=>$export->geoplugin_latitude,
//            'longitude'=>$export->geoplugin_longitude
//        ];
//
//        return $coordinates;
//    }
}