<?php

include 'settings.php';

require_once 'vendor/autoload.php';

use transit_realtime\FeedMessage;

function getVehiclePositions(){
	$data = file_get_contents(GTFS_VehiclePositions_url);
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
	$data = file_get_contents(GTFS_TripUpdates_url);
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
	$data = file_get_contents(GTFS_ServiceAlerts_url);
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
		$current_positions = getVehiclePositions();

		$use_db = true;
		if ($use_db){
			$link = mysqli_connect($dbs[$dbs_key]['host'], $dbs[$dbs_key]['user'], $dbs[$dbs_key]['password'], $dbs[$dbs_key]['name']);
			if (mysqli_connect_errno()){
				$use_db = false;
			}
		}

		if ($use_db){
			$vehicle_history = intval($_REQUEST['vehicle_history']);
			if ($vehicle_history <= 0)
				$vehicle_history = 0; // set default history to return
			if ($vehicle_history > 10)
				$vehicle_history = 10; // sex maximum

			// grab historical vehicle positions (previous)
			$sql = "SELECT `data`,`date_created` FROM `vehiclepositions` ORDER BY `date_created` DESC LIMIT ".$vehicle_history;
			$sql_result =  mysqli_query($link, $sql) or die("Error: ".mysqli_error($link));
			$historical_data = array();
			$previous_md5 = '';
			while($row = mysqli_fetch_assoc($sql_result)){
				$date_created = $row['date_created'];
				if (empty($previous_md5)) // save the most recent (first) md5 of content to determine if we should save or discard current_positions
					$previous_md5 = hash('md5', $row['data']);
				$historical_data[$date_created] = json_decode($row['data'], true);
			}
			mysqli_free_result($sql_result);

			$current_positions_json = json_encode($current_positions);

			if (hash('md5', $current_positions_json) !== $previous_md5){ // current is different than previous, save it
				// save latest data
				$sql = "INSERT INTO `vehiclepositions` (`data`,`date_created`) VALUES ('".mysqli_real_escape_string($link, $current_positions_json)."', NOW())";
				mysqli_query($link, $sql) or die("Error: ".mysqli_error($link));
			}
			else{
				array_shift($historical_data); // remove first element as it is a duplicate of current positions
			}

			foreach($current_positions as $current_key => $current_position){ // merge historical data with current positions
				foreach($historical_data as $historical_key => $historical_datum){
					$current_positions[$current_key]['previous'][$historical_key] = $historical_datum[$current_key];
				}
			}
		}

		$result = $current_positions;
		break;
	case 'tripupdates':
		$result = getTripUpdates();
		break;
	case 'servicealerts':
		$result = getServiceAlerts();
		break;
}

echoJSON($result, $_REQUEST['callback']);

if ($use_db){
	mysqli_close($link);
}

?>