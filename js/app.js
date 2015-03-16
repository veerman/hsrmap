var app = {};

$(document).one('pageshow', function(event){
	init();
});

function init(){
	console.log('init');
	app = {
		'nav': new appNav(),
		'map': new appMap({'lat': 43.255586, 'lng': -79.873151, 'moveMarker_interval': 5000, 'label':{'width':16,'height':20,'radius':6}}), // hamilton city hall, 22, 20
		'bus': new appBus({'service_url': 'www.busweb.hamilton.ca', 'api': 'gtfs', 'vehiclepositions_interval': 10000}), // service/gtfs
		'timer': new appTimer({'id': '#timer'})
	};

	document.title = 'hsrmap.ca - Mobile HSR Map';

	app.map.createMap(function(){ // init map
		app.bus.getRoutesInfo(function(){ // load transit routes information for reference
			app.bus.followVehiclePositions(function(data){ // toggle vehicle position timer
				app.bus.getVehiclePositions(function(data){ // grab vehicle positions
					//console.log(data);
					var updated = {};
					var vehicle_ids = {}; // used to determine which vehicle ids have been removed
					$.each(data, function(key, val){
						if ((!app.bus.vehiclepositions.hasOwnProperty(key)) || (data[key].lat !== app.bus.vehiclepositions[key].lat && data[key].lng !== app.bus.vehiclepositions[key].lng)){ // doesn't have vehicle id OR vehicle position has changed
							updated[key] = data[key];
							vehicle_ids[key] = true;
						}
						else{ // vehicle position unchanged
							vehicle_ids[key] = true;
						}
					});
					$.each(app.bus.vehiclepositions, function(key, val){ // remove markers not contained in the vehiclepositions feed
						if (!vehicle_ids.hasOwnProperty(key)){
							console.log('removed vehicle ' + key);
							app.map.clearMarker({'marker_id': key});
						}
					});

					console.log('vehiclepositions total:' + Object.keys(data).length + ' changed:' + Object.keys(updated).length);
					app.bus.vehiclepositions = data;

					if (Object.keys(updated).length > 0){
						//app.map.moveMarker_time = app.timer.seconds * 1000;
						app.timer.reset();
					}

					$.each(updated, function(key, val){
						var labelPos = app.map.getLabelPosition({'rotation':val.bearing});
						var params = $.extend({}, val,{
							'marker_id': key,
							'marker_params': {
								'icon': {
									'path': 'm 0,0 c -2.79625,0 -5.06625,-2.26875 -5.06625,-5.065 0,-0.205 0.0125,-0.405 0.0363,-0.60375 l -0.0363,0.0412 0.0363,-0.05 c 0.21875,-1.81 1.39125,-3.325 2.99875,-4.02875 l 1.98375,-2.66625 1.995,2.63 c 1.65125,0.68875 2.85875,2.225 3.08,4.06625 l 0.0388,0.0487 -0.0388,-0.0475 c 0.025,0.2 0.0388,0.40375 0.0388,0.61 0,2.79625 -2.27,5.065 -5.06625,5.065 l 0,0',
									//'path': google.maps.SymbolPath.CIRCLE, // CIRCLE,FORWARD_CLOSED_ARROW
									'scale': 2,
									'fillColor': '#fff',
									'fillOpacity': 2,
									'strokeColor': '#0255a0',
									'strokeOpacity': 1,
									'strokeWeight': 2,
									'rotation': val.bearing, // rotate the pointer
									//'size': new google.maps.Size(71, 71),
									//'origin': new google.maps.Point(0, 0),
									'anchor': new google.maps.Point(0, -8),
									//'anchor': new google.maps.Point(0, 0),
									//'scaledSize': new google.maps.Size(25, 25)
								},
								'labelContent': app.bus.routes_info[val.route_id].abbr,
								'labelAnchor': new google.maps.Point(labelPos.x,labelPos.y), // x-values increase to the right and y-values increase to the top.
								//'labelAnchor': new google.maps.Point(10, -10), // x-values increase to the right and y-values increase to the top.
								'labelClass': 'marker-label'
							}
						});
						params.onclick_callback = function(){
							var route_info = app.bus.routes_info[params.route_id];
							var content = [
								'<p>Route: ' + route_info.abbr + '</p>',
								'<p>Name: ' + route_info.name + '</p>',
								(params.route_dir_id !== undefined ? '<p>Direction: ' + route_info.drinfos[params.route_dir_id].dirname + '</p>' : ''),
								'<p>Bus ID: ' + this.id + '</p>',
								(params.speed !== undefined && params.speed > 0 ? '<p>Speed: ' + (params.speed * 1.60934).toFixed(1) + 'km/h</p>' : '')
							].join('');
							app.map.infowindow.setContent(app.map.createInfoWindowHTML({'content':content}));
							app.map.infowindow.open(app.map.map, this);
							app.bus.getRoutePath(params.route_id + '0', function(route0){ // grab both directions of route to form loop
								app.bus.getRoutePath(params.route_id + '1', function(route1){
									var routes = [];
									$.each(route0[0].points, function(key, val){ // each points is a collection of lines
										routes.push(val); // add collection to routes
									});
									$.each(route1[0].points, function(key, val){ // each points is a collection of lines
										routes.push(val); // add collection to routes
									});
									app.map.clearRoutes(); // remove previous route path from map
									$.each(routes, function(key1, val1){ // for each collection of lines
										var pts = [];
										$.each(val1, function(key2, val2){
											pts.push(new google.maps.LatLng(val2.lat, val2.lon));
										});
										app.map.drawRoute({'path': pts});
									});
								});
							});
						}
						app.map.drawMarker(params);
					});
				});
			});
		});
	});
}
