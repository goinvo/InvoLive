<!DOCTYPE HTML>
<html>
<head>


	<link rel="stylesheet" media="print" type="text/css" href="/css/print.css">
	<link href="/css/lib/bootstrap/bootstrap.css" rel="stylesheet">
	<link href="/css/lib/chosen/chosen.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="/css/global.css">

	<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
	<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
	<script type="text/javascript" src ="/js/lib/chosen/chosen.jquery.js"></script>
	<script type="text/javascript" src ="/js/lib/bootstrap/bootstrap.min.js"></script>
	<script type="text/javascript" src ="/js/lib/mustache/mustache.js"></script>
	<script type="text/javascript" src ="/js/lib/chart/Chart.js"></script>
	<script type="text/javascript" src ="/js/lib/moment/moment.js"></script>
	<script type="text/javascript" src ="/js/lib/list/list.js"></script>

	<script src="js/queries.js"></script>
	<script src="js/visualizations.js"></script>
	<script src="js/live.js"></script>

	<script>
		$(document).ready(function(){
			live.queries.initialize();
			live.visualizations.initialize();
		});
	</script>

</head>
<body>

	<div class="container">

		<h1 style="margin-top:20px">Invo Live</h1>

		<hr style="width:100%">

		<div id="queries" class="row">
			<div class="span3">
				<h4> Who </h4>
				<select class="selector" data-placeholder="Everyone" multiple="" tabindex="5" id="selector-user">
				</select>
			</div>
			<div class="span3">
				<h4> What </h4>
				<select class="selector" data-placeholder="Anything" tabindex="5" id="selector-event">
				</select>
			</div>
			<div class="span3">
				<h4> When </h4>
				<select class="selector-single" id="selector-time" style="width:140px;" >
					<option value="lastday">Last day</option>
					<option selected="selected" value="lastmonth">Last Month</option>
					<option value="alltime">All time</option>
				</select>
			</div>
			<div class="span3" >

				<h4>  &zwnj;</h4>
				<button id="button-query" class="btn btn-success  pull-right" type="button">
					Query
				</button></li>
			</div>
		</div>

		<hr>

		<h1 id="nodata" style="display:none"> No data collect yet. Try again later.</h1>

		<div id="results">

			<h1> At a glance </h1>

			<div class="row" id="visualizations">
				<div id="chart-container" class="span8 nomargin"> 
				</div>
				<div class="span4 nomargin" > 
					<canvas id="dchart" width=250 height=250 class="pull-right"></canvas>
				</div>
			</div>
			<hr>

			<h1 > Stats </h1>

			<div class="row">

				<div id="user-list-container" class="span12"> 

				</div>
			</div>

		</div>


	</div>




	<?php
	include_once "includes/templates.php";
	?>



</body>
</html>