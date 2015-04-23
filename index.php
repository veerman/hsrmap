<?

include 'settings.php';

unset($city['service_url']); // remove service url

$app_settings = array(
	'city' => $city,
	'vehiclepositions_api' => $city['abbr'] === 'hamilton' ? (empty($_REQUEST['api']) ? 'gtfs' : $_REQUEST['api']) : 'service',
	'vehiclepositions_interval' => 20000, // how often to request new vehicle data (in ms)
	'moveMarker_interval' => 15000 // how long an animation should take to complete (in ms)
);

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta property="og:type" content="website" />
		<meta property="og:url" content="http://hsrmap.ca" />
		<meta property="og:image" content="http://hsrmap.ca/images/bus_wide.jpg" />
		<meta property="og:title" content="Mobile HSR Map - Live Bus Tracker" />
		<meta property="og:site_name" content="hsrmap.ca" />
		<meta property="fb:admins" content="58016494" />
		<title>hsrmap.ca - Mobile HSR Map</title>
		<link rel="stylesheet" href="css/jquery.mobile-1.4.5.min.css">
		<style>
			#map-page, #map-canvas {
				width: 100%;
				height: 100%;
				padding: 0;
			}

			.marker-label {
				font-size: small;
				white-space: nowrap;
				width: 18px;
				text-align: center;
				border-radius: 30%;
				/*border: 1px solid #0255a0;*/
				/*background-color: #fff;*/
			}

			.form_center{
				margin: 0 auto;
				margin-left: auto;
				margin-right: auto;
				align: center;
				text-align: center;
				width:300px;
			}
		</style>
		<script>
			var app_settings = <?=json_encode($app_settings) ?>;
		</script>
		<script src="js/jquery.min.js"></script>
		<script src="js/init.js"></script>
		<script src="js/jquery.mobile-1.4.5.min.js"></script>
		<script src="js/nav.js"></script>
		<script	src="js/map.js"></script>
		<script src="js/bus.js"></script>
		<script src="js/timer.js"></script>
		<script src="js/app.js"></script>
		<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script src="js/markerwithlabel_packed.js"></script>
	</head>
	<body>
		<div data-role="page" id="map-page">
			<div data-role="panel" data-display="overlay" class="menu_nav" data-position-fixed="true">
				<ul data-role="listview" class="nav-menu">
					<li data-role="list-divider">Mobile HSR Map</li>
					<li><a href="http://www.hamilton.ca/CityServices/Transit/CurrentSchedules/" class="ui-icon-clock" target="_blank">HSR Current Schedules</a></li>
					<li><a href="javascript:void(0)" class="menu_about ui-icon-info">About</a></li>
					<li data-role="list-divider">Other Apps (links)</li>
					<li><a href="http://realtime-hsr.cloudapp.net/" class="ui-icon-cloud" target="_blank">Realtime HSR</a></li>
					<li><a href="http://www.busweb.hamilton.ca:8008/hiwire?.a=iRealTimeDisplay" class="ui-icon-cloud" target="_blank">HSR's Bus Web</a></li>
				</ul>
			</div><!-- /menu	-->
			<div class="page_header" data-role="header" data-tap-toggle="false" data-position="fixed">
				<a href="javascript:void(0)" data-role="button" data-inline="true" data-icon="bars" data-iconpos="notext" class="ui-btn-left menu_toggle"></a>
				<span class="ui-title">Last Update <span id="timer"></span>s</span>
			</div><!-- /header	-->
			<div role="main" class="ui-content" id="map-canvas">
				<!-- map loads here... -->
			</div>
			<div data-role="popup" id="about" data-dismissible="false" data-overlay-theme="b">
				<div data-role="header">
					<h1>About</h1>
				</div><!-- /header	-->
				<div data-role="content" class="form_center">
					<div style="margin:0 auto;margin-left:auto;margin-right:auto;align:center;text-align:center;">
						<p style="font-size:smaller;">Suggestions?</p>
						<p><a href="https://twitter.com/veerman" class="twitter-follow-button" data-show-count="false">Follow @veerman</a></p>
						<p style="font-size:x-small;">Created by <a href="http://steve.veerman.ca" target="_blank">Steve Veerman</a></p>
					</div>
					<a href="javascript:void(0)" data-role="button" data-icon="check" class="close_popup">Close</a>
				</div><!-- /content	-->
			</div><!-- /popup	-->
		</div><!-- /page -->
	</body>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-24658326-8', 'auto');
	  ga('send', 'pageview');
	</script>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
</html>
