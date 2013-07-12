<!DOCTYPE HTML>
<html>
<head>


	<link rel="stylesheet" media="print" type="text/css" href="/css/print.css">
	<link href="/css/lib/bootstrap/bootstrap.css" rel="stylesheet">
	<link href="/css/lib/chosen/chosen.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="/css/global.css">

	<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
	<script type="text/javascript" src ="/js/lib/chosen/chosen.jquery.js"></script>
	<script type="text/javascript" src ="/js/lib/bootstrap/bootstrap.min.js"></script>
	<script type="text/javascript" src ="/js/lib/mustache/mustache.js"></script>
	<script type="text/javascript" src ="/js/lib/chart/Chart.js"></script>

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
				<select class="selector" data-placeholder="Everyone" multiple="" tabindex="3" id="selector-user">
				</select>
			</div>
			<div class="span3">
				<h4> What </h4>
				<select class="selector" data-placeholder="Anything" multiple="" tabindex="3" id="selector-event">
				</select>
			</div>
			<div class="span2">
				<h4> When </h4>
				<select class="selector-single" id="selector-time" style="width:140px;" >
					<option value="volvo">Last hour</option>
					<option value="saab">Last day</option>
					<option value="mercedes">Last Month</option>
					<option value="mercedes">All time</option>
				</select>
			</div>
			<div class="span2 span-grouping disabled" >
				<h4> Order by </h4>
				<select class="selector-single" data-placeholder="" id="selector-grouping" style="width:140px;">
					<option value=""></option>

				</select>
			</div>

			<div class="span2">
				<h4>  &zwnj;</h4>
				<button id="button-query" class="btn btn-success  pull-right" type="button">
          		Query
          </button></li>
			</div>
		</div>

<!-- 		<div class="row">
			<div class="span2">
				 <button class="btn btn-large btn-success" type="button">Query me</button>
			</div>
		</div> -->

		<hr>

		<div class="row" id="visualizations">
			<div class="span8 nomargin"> 
				<canvas id="chart" width=600 height=250 ></canvas>
			</div>
			<div class="span4 nomargin" > 
				<canvas id="dchart" width=250 height=250 class="pull-right"></canvas>
			</div>
		</div>


	</div>

	<script type="text/html" id="option-template">
		{{#.}}
			<option value={{.}}>{{.}}</option>
		{{/.}}
	</script>



</body>
</html>