<?php

require_once 'vendor/autoload.php';

use transit_realtime\FeedMessage;

function getVehiclePositions(){
	$data = file_get_contents("http://opendata.hamilton.ca/GTFS-RT/GTFS_VehiclePositions.pb");
	$feed = new FeedMessage();
	$feed->parse($data);

	$results = array();
	foreach ($feed->getEntityList() as $entity){
	  if ($entity->hasVehicle()){
	  	$result = array();

		  if ($entity->hasVehicle()){
		  	$vehicle = $entity->getVehicle();
		  	$result = array_merge($result, array(
		  		//'congestion_level' => $vehicle->getCongestionLevel(),
		  		'stop_id' => $vehicle->getStopId(),
		  		'timestamp' => $vehicle->getTimestamp()
		  	));

				if ($vehicle->hasVehicle()){
					$descriptor = $vehicle->getVehicle();
			  	$result = array_merge($result, array(
			  		'vehicle_id' => $descriptor->getId(),
			  		'vehicle_label' => $descriptor->getLabel()
			  	));
			  }

		  	if ($vehicle->hasPosition()){
					$position = $vehicle->getPosition();
					$result = array_merge($result, array(
						'lat' => $position->getLatitude(),
						'lng' => $position->getLongitude(),
						'speed' => $position->getSpeed(),
						'bearing' => $position->getBearing(),
						'odometer' => $position->getOdometer()
					));
				}

				if ($vehicle->hasTrip()){
					$trip = $vehicle->getTrip();
					$result = array_merge($result, array(
						'route_id' => $trip->getRouteId(),
						//'schedule_relationship' => $trip->getScheduleRelationship(),
						'trip_id' => $trip->getTripId()
					));
				}

			  if ($entity->hasId()){
		  		$result = array_merge($result, array(
		  			'vehicle_id' => $entity->getId()
		  		));
		  	}
		  	ksort($result);

				$vehicle_id = $result['vehicle_id'];
				unset($result['vehicle_id']);
				$results[$vehicle_id] = $result;
		  }
	  }
	}
	return $results;
}

function getTripUpdates(){
	$data = file_get_contents("http://opendata.hamilton.ca/GTFS-RT/GTFS_TripUpdates.pb");
	$feed = new FeedMessage();
	$feed->parse($data);

	$results = array();
	foreach ($feed->getEntityList() as $entity){
	  if ($entity->hasTripUpdate()){
	  	$result = array();

	  	$tripupdate = $entity->getTripUpdate();

	  	$result = array_merge($result, array(
	  		'timestamp' => $tripupdate->getTimestamp()
	  	));

			if ($tripupdate->hasVehicle()){
				$descriptor = $tripupdate->getVehicle();
		  	$result = array_merge($result, array(
		  		'vehicle_id' => $descriptor->getId(),
		  		'vehicle_label' => $descriptor->getLabel()
		  	));
		  }

			if ($tripupdate->hasTrip()){
				$trip = $tripupdate->getTrip();
				$result = array_merge($result, array(
					'route_id' => $trip->getRouteId(),
					//'schedule_relationship' => $trip->getScheduleRelationship(),
					'trip_id' => $trip->getTripId()
				));
			}

		  if ($entity->hasId()){
	  		$result = array_merge($result, array(
	  			'vehicle_id' => $entity->getId()
	  		));
	  	}

			$stoptimeupdates = array();
			foreach($tripupdate->getStopTimeUpdateList() as $stoptimeupdate){ // getStopTimeUpdate
				$stoptimeupdate_tmp = array(
					'stop_id' => $stoptimeupdate->getStopId(),
					//'schedule_relationship' => $stoptimeupdate->getScheduleRelationship()
				);

				if ($stoptimeupdate->hasDeparture()){
					$stoptimeupdate_tmp['delay'] = $stoptimeupdate->getDeparture()->getDelay();
					$stoptimeupdate_tmp['time'] = $stoptimeupdate->getDeparture()->getTime();
				}
				array_push($stoptimeupdates, $stoptimeupdate_tmp);
			}
			if (count($stoptimeupdates)){
				$result = array_merge($result, array(
					'stoptimeupdates' => $stoptimeupdates
				));
			}

	  	ksort($result);
	  	array_push($results, $result);
	  }
	}
	return $results;
}

function getServiceAlerts(){
	$data = file_get_contents("http://opendata.hamilton.ca/GTFS-RT/GTFS_ServiceAlerts.pb");
	$feed = new FeedMessage();
	$feed->parse($data);

	$results = array();
	foreach ($feed->getEntityList() as $entity){
		print_r($entity);exit;
	  if ($entity->hasServiceAlert()){
	  	$result = array();

	  	$servicealert = $entity->getServiceAlert();

	  	$result = array_merge($result, array(
	  		'timestamp' => $servicealert->getTimestamp()
	  	));

	  	ksort($result);
	  	array_push($results, $result);
	  }
	}
	return $results;
}

function echoJSON($object, $callback){
	if (isset($callback)){
		header('Content-type: text/javascript');
		echo $callback.'('.json_encode($object).');';
	}
	else{
		header('Content-type: application/json');
		echo json_encode($object);
	}
}

switch (strtolower($_REQUEST['method'])) {
	case 'vehiclepositions':
		$result = getVehiclePositions();
		break;
	case 'tripupdates':
		$result = getTripUpdates();
		break;
	case 'servicealerts':
		$result = getServiceAlerts();
		break;
}

echoJSON($result, $_REQUEST['callback']);

?>