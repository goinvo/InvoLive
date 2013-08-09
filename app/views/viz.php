<!DOCTYPE HTML>
<html>
<head>


	<link rel="stylesheet" media="print" type="text/css" href="css/print.css">
	<link href="css/lib/bootstrap/bootstrap.css" rel="stylesheet">
	<link href="css/lib/chosen/chosen.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/global.css">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet'>

	<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
	<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
	<script type="text/javascript" src ="js/lib/bootstrap/bootstrap.min.js"></script>
	<script type="text/javascript" src ="js/lib/mustache/mustache.js"></script>
	<script type="text/javascript" src ="js/lib/chart/Chart.js"></script>
	<script type="text/javascript" src ="js/lib/moment/moment.js"></script>
	<script type="text/javascript" src ="js/lib/list/list.js"></script>
	<script type="text/javascript" src ="js/lib/sonic/sonic.js"></script>
	<script type="text/javascript" src ="js/lib/scrollto/jquery.scrollTo.js"></script>

	<script src="js/queries.js"></script>
	<script src="js/visualizations.js"></script>
	<script src="js/scores.js"></script>
	<script src="js/live.js"></script>

	<script>
		$(document).ready(function(){

			$('#codename').delay(500).animate({'margin-left': 25, 'opacity' : 1}, 1000);

			live.queries.initialize();
			live.visualizations.initialize();
			initPreloader();
			startPreloader(0);
		});
	</script>

	<title> Invo Live </title>

</head>
<body>

	<div class="container" style="margin-bottom:20px;">

		<div> 
			<h1 style="margin-top:20px; display:inline-block">Invo Live v0.4</h1>
			<h4 id="codename" style="display:inline-block; margin-left:-50px; opacity: 0"> Picasso </h4>
		</div>

		<hr>


		<h1 id="nodata" style="display:none"> No data collect yet. Try again later.</h1>

		<div id="results">

			<div id="results-preloader" style="margin-top:250px;"> </div>

			<div id="results-content">

			</div>


		</div>
	</div>

	<?php
	include_once "includes/templates.php";
	?>
	
</body>
</html>