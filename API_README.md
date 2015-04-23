## API Readme

All endpoints accept callback parameter for JSONP requests.

### gtfs.php?method=vehiclepositions

Return latest vehicle position. Also has experimental vehicle_history parameter to return previously stored vehicle locations eg. &vehicle_history=3

Example Result
```

{
	247: { // vehicle_id
		bearing: 198,
		lat: 43.236949920654,
		lng: -79.845367431641,
		odometer: 54334300,
		route_id: "2989",
		speed: 8,
		stop_id: "1347",
		timestamp: 1429760627,
		trip_id: "960188",
		vehicle_label: "404"
	},
	250: {
		bearing: 268,
		lat: 43.209671020508,
		lng: -79.787269592285,
		odometer: 37968300,
		route_id: "2976",
		speed: 0,
		stop_id: "2530",
		timestamp: 1429760591,
		trip_id: "956867",
		vehicle_label: "407"
	}
}

```

### gtfs.php?method=tripupdates

Return trip updates (stop updates).

Example Result
```

[
	{
		route_id: "2976",
		stoptimeupdates: [
		{
			stop_id: "2431",
			delay: 0,
			time: 1429761600
		},
		{
			stop_id: "2432",
			delay: 0,
			time: 1429761634
		}
		],
		timestamp: 1429760845,
		trip_id: "956869",
		vehicle_id: "956869",
		vehicle_label: "514"
	},
	{
		route_id: "2976",
		stoptimeupdates: [
		{
			stop_id: "2530",
			delay: 120,
			time: 1429760700
		},
		{
			stop_id: "2539",
			delay: 107,
			time: 1429760717
		}
		],
		timestamp: 1429760867,
		trip_id: "956735",
		vehicle_id: "956735",
		vehicle_label: "407"
	}
]

```

### gtfs.php?method=servicealerts

This endpoint is not especially useful at the moment.

Example Result
```

[
	{
		timestamp: 1429760867
	}
]

```

### http://hsrmap.ca/service.php?method=GetListOfLines

Return all lines.

Example Result
```

{
	2938: {
		abbr: "01",
		name: "KING",
		drinfos: {
			29380: {
				dirname: "Eastbound"
			},
			29381: {
				dirname: "Westbound"
			}
		}
	},
	2939: {
		abbr: "02",
		name: "BARTON",
		drinfos: {
			29390: {
				dirname: "Eastbound"
			},
			29391: {
				dirname: "Westbound"
			}
		}
	}
}

```

### http://hsrmap.ca/service.php?method=GetLineTrace&lineDirId=29380

Return lineDirId route/trace points to plot on map.

Example Result
```

[
	{
		loncenter: -79.8446325,
		latcenter: 43.243153,
		lonwest: -79.943134,
		loneast: -79.746131,
		latnorth: 43.263338,
		latsouth: 43.222968,
		points: [
			[
				{
					lon: -79.8684,
					lat: 43.252444
				},
				{
					lon: -79.868347,
					lat: 43.25242
				},
				{
					lon: -79.765457,
					lat: 43.22865
				}
			]
		]
	}
]

```

### http://hsrmap.ca/service.php?method=GetStopsForLine&lineDirId=29380,29381

Return stops for specific lineDirIds.

Example Result
```

{
	29380: [
		{
			stopid: 355415,
			abbr: "92702",
			ivrnum: "1701",
			name: "HAMILTON GO CENTRE PLATFORM 18",
			lon: -79.868584,
			lat: 43.252496
		},
		{
			stopid: 355260,
			abbr: "34000",
			ivrnum: "1001",
			name: "UNIVERSITY PLAZA PLATFORM 1",
			lon: -79.942808,
			lat: 43.258396
		}
	],
	29381: [
		{
			stopid: 2642,
			abbr: "44050",
			ivrnum: "2527",
			name: "FIESTA MALL LOOP",
			lon: -79.746144,
			lat: 43.222896
		},
		{
			stopid: 2504,
			abbr: "44015a",
			ivrnum: "2559",
			name: "HIGHWAY 8 at GRAY",
			lon: -79.744624,
			lat: 43.223632
		}
	]
}

```

### http://hsrmap.ca/service.php?method=GetTravelPoints&lineDirId=29380

Experimental alternative to gtfs.php?method=vehiclepositions

Return latest vehicle position.

Example Result
```

{
	254: {
		block_id: 415843,
		escha: -110,
		lfm: false,
		route_dir_id: 29760,
		route_id: 2976,
		pattern_id: 18912,
		trip_id: 956713,
		vehicle_number: "411",
		bearing: 251.56505117708,
		lat: 43.262533,
		lng: -79.891436,
		time: 36426,
		estimated_points: [
			{
				bearing: 251.56505117708,
				lat: 43.262533,
				lng: -79.891436,
				time: 36426
			},
			{
				bearing: 251.56505117708,
				lat: 43.262518,
				lng: -79.891501,
				time: 36427
			}
		],
		scheduled_points: [ ]
	},
	256: {
		block_id: 415836,
		escha: -496,
		lfm: false,
		route_dir_id: 29760,
		route_id: 2976,
		pattern_id: 18909,
		trip_id: 956731,
		vehicle_number: "413",
		bearing: 280.81783358484,
		lat: 43.265895,
		lng: -79.952388,
		time: 36426,
		estimated_points: [
			{
				bearing: 280.81783358484,
				lat: 43.265895,
				lng: -79.952388,
				time: 36426
			},
			{
				bearing: 280.81783358484,
				lat: 43.265907,
				lng: -79.952473,
				time: 36427
			}
		],
		scheduled_points: [ ]
	}
}

```
