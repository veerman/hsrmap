function appMap(params){
	var self = this;

	self.createMap = function(callback){
		var defaultLatLng = new google.maps.LatLng(43.255586, -79.873151); // hamilton city hall

		if (navigator.geolocation){
			function success(pos){
				self.drawMap({'callback': callback, 'latlng': new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude)});
			}

			function fail(error){
				self.drawMap({'callback': callback, 'latlng': defaultLatLng});
			}

			// Find the users current position.  Cache the location for 5 minutes, timeout after 6 seconds
			navigator.geolocation.getCurrentPosition(success, fail, {maximumAge: 500000, enableHighAccuracy:true, timeout: 6000});
		}else{
			self.drawMap({'callback': callback, 'latlng': defaultLatLng}); // No geolocation support, show default map
		}
	}

	self.drawMap = function(params){
		var map_params = {
			zoom: 16,
			center: params.latlng,
			disableDefaultUI: true,
			panControl: false,
			zoomControl: true,
			zoomControlOptions: {
				style: google.maps.ZoomControlStyle.LARGE
			},
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		map_params = $.extend({}, map_params, params.map_params);

		self.map = new google.maps.Map(document.getElementById("map-canvas"), map_params);
		self.infowindow = new google.maps.InfoWindow();

		google.maps.event.addListenerOnce(self.map, 'idle', params.callback);
	}

	self.refreshMap = function(params){
		self.map('refresh');
	}

	self.clearMarkers = function(){
		for (var member in self.markers) delete self.markers[member].setMap(null);
	}

	self.clearMarker = function(params){
		if (self.markers[params.marker_id] !== undefined)
			delete self.markers[params.marker_id].setMap(null);
	}

	self.drawMarker = function(params){
		var marker_params = {
			'map': self.map,
			'zIndex': 99999
		}
		marker_params = $.extend({}, marker_params, params.marker_params);
		var marker_id = params.marker_id !== undefined ? params.marker_id : Object.keys(markers).length;
		marker_params.id = marker_id;

		marker_params.position = new google.maps.LatLng(params.lat, params.lng);

		params.animate = true;
		if (params.animate && self.markers[marker_id] !== undefined)
			self.moveMarker({'marker_id': marker_id, 'from': {'position': self.markers[marker_id].getPosition(), 'rotation': self.markers[marker_id].icon.rotation}, 'to': {'position': marker_params.position, 'rotation': params.bearing}, 'index': 0});
		else{
			self.clearMarker({'marker_id': marker_id});
			self.markers[marker_id] = new MarkerWithLabel(marker_params);
		}

		google.maps.event.addListener(self.markers[marker_id], 'click', params.onclick_callback);
		google.maps.event.addListener(self.infowindow, 'closeclick', function(){
		   self.clearRoutes();
		});
	}

	self.moveMarker = function(params){ // marker_id, from, to, index
		var limit = 20;
		var lat = params.from.position.lat() + (params.index / limit) * (params.to.position.lat() - params.from.position.lat());
		var lng = params.from.position.lng() + (params.index / limit) * (params.to.position.lng() - params.from.position.lng());

		var rotation_difference = params.to.rotation - params.from.rotation;
		if (rotation_difference < 0){ // is negative, counter-clockwise
			cw = 360 + rotation_difference;
			ccw = rotation_difference;
		}
		else{ // is positive, clockwise
			cw = rotation_difference;
			ccw = 360 - rotation_difference;
		}
		var rotation_degrees = Math.abs(cw) > Math.abs(ccw) ? ccw : cw;
		var rotation = params.from.rotation + (params.index / limit) * (rotation_degrees);
		var icon = jQuery.extend({}, self.markers[params.marker_id].icon);;

		icon.rotation = rotation;
		icon.fillColor = '#f8cf09';
		if (params.index === limit)
			icon.fillColor = '#fff';
		self.markers[params.marker_id].setIcon(icon);
		self.markers[params.marker_id].setPosition(new google.maps.LatLng(lat, lng));
	  if (params.index < limit){ // call the next "frame" of the animation
	  	params.index++;
	    setTimeout(function(){
	      self.moveMarker(params);
	    }, (self.moveMarker_time / limit)); // 25 seconds split over 100 frames
	  }
	}

	self.clearRoutes = function(){
		for (var member in self.routes) delete self.routes[member].setMap(null);
	}

	self.drawRoute = function(params){
		var route_params = {
			path: params.path,
			strokeColor: "#ff0000",
			strokeOpacity: 0.6,
			strokeWeight: 5
		}
		route_params = $.extend({}, route_params, params.route_params);
		var route_id = params.route_id !== undefined ? params.route_id : Object.keys(self.routes).length;

		self.routes[route_id] = new google.maps.Polyline(route_params);
		self.routes[route_id].setMap(self.map);
	}

	self.createInfoWindowHTML = function(params){
		return [
			'<div id="content" style="width:120px;font-size:x-small;">',
				'<div id="bodyContent">',
					params.content,
				'</div>',
			'</div>'
		].join('');
	}

	self.init = function(params){
		console.log('init ' + self.constructor.name);

		self.map = null;
		self.markers = {};
		self.routes = {}
		self.moveMarker_time = 15000;
	}(params);
}
