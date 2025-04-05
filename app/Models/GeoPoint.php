<?php

namespace App\Models;

class GeoPoint
{
    public $latitude;
    public $longitude;

    /**
     * Point constructor.
     * @param $latitude
     * @param $longitude
     */
    public function __construct($latitude = null, $longitude = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }


    public function parse($point)
    {
        if($point) {
            $location = str_replace('point', '', strtolower($point));
            $location = ltrim($location, '(');
            $location = rtrim($location, ')');
            $location = explode(' ', $location);

            $this->latitude = isset($location[0]) ? floatval($location[0]) : null;
            $this->longitude = isset($location[1]) ? floatval($location[1]) : null;
        }
    }

    public function toArray()
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function isEmpty()
    {
        return (empty($this->latitude) || empty($this->longitude));
    }

    public function __toString()
    {
        if($this->isEmpty()) {
            return '';
        }
        return "POINT({$this->latitude} {$this->longitude})";
    }


}
