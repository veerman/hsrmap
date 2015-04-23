function appBus(params){
	var self = this;

	self.getRoutesInfo = function(callback){
		$.ajax({
		  type: 'GET',
		  url: 'service.php',
		  data: {'city':self.city,'method':'GetListOfLines'},
			dataType: 'json'
		})
		.done(function(data, textStatus, jqXHR){
			self.routes_info = data;
			callback(data);
	  })
	  .fail(function(){
	    console.log('getRoutes ajax fail');
	  });
	}

	self.getRoutePath = function(route, callback){
		$.ajax({
		  type: 'GET',
		  url: 'service.php',
		  data: {'city':self.city,'method':'GetLineTrace','lineDirId':route},
			dataType: 'json'
		})
		.done(function(data, textStatus, jqXHR){
			callback(data);
	  })
	  .fail(function(){
	    console.log('getRoutePath ajax fail');
	  });
	}

	self.getRouteStops = function(route, callback){
		$.ajax({
		  type: 'GET',
		  url: 'service.php',
		  data: {'city':self.city,'method':'GetStopsForLine','lineDirId':route},
			dataType: 'json'
		})
		.done(function(data, textStatus, jqXHR){
			callback(data);
	  })
	  .fail(function(){
	    console.log('getRouteStops ajax fail');
	  });
	}

	self.getVehiclePositions = function(callback){
		if (self.vehiclepositions_api === 'gtfs'){
			self.getVehiclePositionsGTFS(callback);
		}
		else{
			self.getVehiclePositionsService(callback);
		}
	}

	self.getVehiclePositionsService = function(callback){
		$.ajax({
		  type: 'GET',
		  url: 'service.php',
		  data: {'city':self.city,'method':'GetTravelPoints'},
			dataType: 'json'
		})
		.done(function(data, textStatus, jqXHR){
			callback(data);
	  })
	  .fail(function(){
	    console.log('getVehiclePositions ajax fail');
	  });
	}

	self.getVehiclePositionsGTFS = function(callback){
		$.ajax({
		  type: 'GET',
		  url: 'gtfs.php?method=vehiclepositions',
			contentType: 'application/json; charset=UTF-8',
			dataType: 'json'
		})
		.done(function(data, textStatus, jqXHR){
			callback(data);
	  })
	  .fail(function(){
	    console.log('getVehiclePositionsGTFS ajax fail');
	  });
	}

	self.followVehiclePositions = function(callback){
		callback();
		self.followVehiclePositions_callback = callback;
		setInterval(function(){
			self.followVehiclePositions_callback();
		}, self.vehiclepositions_interval);
	}

	self.getRouteName = function(params){
		var route = self.routes_info[params.route_id];
		var name = '';
		if (params.format === 'numbername'){
			name = route.abbr + ' - ' + route.name;
		}
		else if (params.format === 'numbernamedir' && params.route_dir_id !== undefined){
			name = route.abbr + ' - ' + route.name + ' - ' + route.drinfos[params.route_dir_id].dirname;
		}
		else{ // default is number
			name = route.abbr;
		}
		return name;
	}

	self.init = function(params){
		console.log('init ' + self.constructor.name);

		self.city = params.city;
		self.vehiclepositions_api = params.vehiclepositions_api;
		self.vehiclepositions = {};
		self.vehiclepositions_interval = params.vehiclepositions_interval; // 10000
	}(params);
}