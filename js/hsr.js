function appHSR(params){
	var self = this;

	self.followVehiclePositions = function(callback){
		callback();
		self.followVehiclePositions_callback = callback;
		setInterval(function(){
			self.followVehiclePositions_callback();
		}, 20000);
	}

	self.getVehiclePositions = function(callback){
		$.ajax({
		  type: 'GET',
		  url: 'feed.php?method=vehiclepositions',
			contentType: 'application/json; charset=UTF-8',
			dataType: 'json'
		})
		.done(function(data, textStatus, jqXHR){
			callback(data);
	  })
	  .fail(function(){
	    console.log('getVehiclePositions ajax fail');
	  });
	}

	self.getVehiclePositionsBusWeb = function(callback){
		$.ajax({
		  type: 'POST',
		  url: 'proxy.php?url=http://www.busweb.hamilton.ca:8008/RealTimeManager',
		  data: {'version':'1.1','method':'GetTravelPoints','params':{'travelPointsReqs':[{'lineDirId':'29420','callingApp':'RMD'}],'interval':10}},
			contentType: 'application/json; charset=UTF-8',
			dataType: 'json'
		})
		.done(function(data, textStatus, jqXHR){
			callback(data);
	  })
	  .fail(function(){
	    console.log('getVehiclePositionsBusWeb ajax fail');
	  });
	}

	self.getRoutes = function(callback){
		$.ajax({
		  type: 'GET',
		  url: 'routes/data.json',
			dataType: 'json'
		})
		.done(function(data, textStatus, jqXHR){
			self.routes_info = data;
			//callback(data);
	  })
	  .fail(function(){
	    console.log('getRoutes ajax fail');
	  });
	}

	self.lookupRoute = function(params){
		var name = self.routes_info[params.route_id + '0'].name.split(' - ');
		params.format = params.format !== undefined ? params.format : 'numbername';
		if (params.format === 'number'){
			name.pop(); // remove direction
			name.pop(); // remove name
		}
		else if (params.format === 'numbername'){
			name.pop(); // remove direction				}
		}
		return name.join(' - ');
	}

	self.lookupRouteNumber = function(route){
		var name = self.routes_info[route + '0'].name.split(' - ');
		name.pop(); // remove direction from name
		name.pop(); // remove name
		return name.join(' - ');
	}

	self.getRoute = function(route, callback){
		$.ajax({
		  type: 'GET',
		  url: 'routes/' + route + '.json',
			dataType: 'json'
		})
		.done(function(data, textStatus, jqXHR){
			callback(data);
	  })
	  .fail(function(){
	    console.log('getRoute ajax fail');
	  });
	}

	self.init = function(params){
		console.log('init ' + self.constructor.name);

		self.vehiclepositions = {};
		self.getRoutes(); // load route lookup information
	}(params);
}