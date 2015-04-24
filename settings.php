<?php

define('GTFS_VehiclePositions_url', 'http://opendata.hamilton.ca/GTFS-RT/GTFS_VehiclePositions.pb');
define('GTFS_TripUpdates_url', 'http://opendata.hamilton.ca/GTFS-RT/GTFS_TripUpdates.pb');
define('GTFS_ServiceAlerts_url', 'http://opendata.hamilton.ca/GTFS-RT/GTFS_ServiceAlerts.pb');

$dbs = array(
	'hsrmap_dev' => array('host' => 'localhost', 'user' => 'hsr', 'password' => 'password', 'name' => 'hsrmap')
);
$dbs_key = 'hsrmap';

$cities = array(
	'hamilton' => array(
		'name' => 'Hamilton, Ontario',
		'lat' => 43.255586, // 43.253793
		'lng' => -79.873151, // -79.746993
		'GTFS_VehiclePositions_url' => 'http://opendata.hamilton.ca/GTFS-RT/GTFS_VehiclePositions.pb',
		'GTFS_TripUpdates_url' => 'http://opendata.hamilton.ca/GTFS-RT/GTFS_TripUpdates.pb',
		'GTFS_ServiceAlerts_url' => 'http://opendata.hamilton.ca/GTFS-RT/GTFS_ServiceAlerts.pb'
	)
);

$city_abbr = empty($_REQUEST['city']) ? 'hamilton' : strtolower($_REQUEST['city']);
$city = $cities[$city_abbr];
if (empty($city)){ // lookup failed, set to default
	$city_abbr = 'hamilton';
	$city = $cities[$city_abbr];
}

$city['abbr'] = $city_abbr;

?>