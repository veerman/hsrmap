var app = {};

$(document).one('pageshow', function(event){
	init();
});

function init(){
	console.log('init');
	app = {
		'nav': new appNav(),
		'map': new appMap(),
		'hsr': new appHSR(),
		'timer': new appTimer({'id': '#timer'})
	};

	app.map.createMap(function(){
		app.hsr.followVehiclePositions(function(data){
			app.hsr.getVehiclePositions(function(data){
				var updated = {};
				var vehicle_ids = {}; // used to determine which vehicle ids have been removed
				$.each(data, function(key, val){
					if ((!app.hsr.vehiclepositions.hasOwnProperty(key)) || (data[key].lat !== app.hsr.vehiclepositions[key].lat && data[key].lng !== app.hsr.vehiclepositions[key].lng)){ // doesn't have vehicle id OR vehicle position has changed
						updated[key] = data[key];
						vehicle_ids[key] = true;
					}
					else{ // vehicle position unchanged
						vehicle_ids[key] = true;
					}
				});
				$.each(app.hsr.vehiclepositions, function(key, val){ // remove markers not contained in the vehiclepositions feed
					if (!vehicle_ids.hasOwnProperty(key)){
						console.log('removed vehicle ' + key);
						app.map.clearMarker({'marker_id': key});
					}
				});

				console.log('vehiclepositions total:' + Object.keys(data).length + ' changed:' + Object.keys(updated).length);
				app.hsr.vehiclepositions = data;

				if (Object.keys(updated).length > 0){
					//app.map.moveMarker_time = app.timer.seconds * 1000;
					app.timer.reset();
				}

				$.each(updated, function(key, val){
					var params = $.extend({}, val,{
						'marker_id': key,
						'marker_params': {
							'icon': {
								'path': google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
								'scale': 5,
								'fillColor': '#fff',
								'fillOpacity': 1,
								'strokeColor': '#0255a0',
								'strokeOpacity': 1,
								'strokeWeight': 2,
								'rotation': val.bearing // rotate the pointer
							},
							'labelContent': app.hsr.lookupRoute({'route_id': val.route_id, 'format': 'number'}),
							'labelAnchor': new google.maps.Point(10, -10),
							'labelClass': 'marker-label'
						}
					});
					params.onclick_callback = function(){
						app.map.infowindow.setContent(app.map.createInfoWindowHTML({'content':'<p>Route: ' + app.hsr.lookupRoute({'route_id': params.route_id}) + '</p><p>Bus ID: ' + this.id + '</p><p>Speed: ' + params.speed + 'km/h</p>'}));
						app.map.infowindow.open(app.map.map, this);
						app.hsr.getRoute(params.route_id + '0', function(route0){ // need to grab both directions of route because there is no way to tell which on the bus is using
							app.hsr.getRoute(params.route_id + '1', function(route1){
								var routes = [];
								$.each(route0.result[0].GoogleMap, function(key, val){
									routes.push(val);
								});
								$.each(route1.result[0].GoogleMap, function(key, val){
									routes.push(val);
								});

								$.each(routes, function(key1, val1){
									var pts = [];
									$.each(val1.Points, function(key2, val2){
										pts.push(new google.maps.LatLng(val2.Lat, val2.Lon));
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
}
