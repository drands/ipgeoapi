<?php
namespace App;

use GeoIp2\Database\Reader;

class GeoIPSearch {

    private $reader;
    
    public function __construct() {
        $this->reader = new Reader(__DIR__ . '/../data/database.mmdb');
    }

    public function search($ip) {
        return $this->reader->city($ip);
    }

    public function parse($record) {
        $region = $record->subdivisions[0] ?? null;
        $data = [
            'ip' => $record->traits->ipAddress,
            'city' => $record->city->name,
            'country' => [
                'code' => $record->country->isoCode,
                'name' => $record->country->name,
            ],
            'postal_code' => $record->postal->code,
            'region' => $region->name ?? null,
            'province' => $record->mostSpecificSubdivision->name,
            'continent' => $record->continent->name,
            'latitude' => $record->location->latitude,
            'longitude' => $record->location->longitude,
            'timezone' => $record->location->timeZone,
            'is_in_eu' => $record->country->isInEuropeanUnion,
        ];
        return json_encode($data);
    }

}