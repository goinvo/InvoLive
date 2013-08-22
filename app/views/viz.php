<!DOCTYPE html>

<html>
<head>
    <link href="css/print.css" media="print" rel="stylesheet" type="text/css">
    <link href="css/lib/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="css/lib/chosen/chosen.css" rel="stylesheet">
    <link href="css/global.css" rel="stylesheet" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400' rel=
    'stylesheet'>
    <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
    <script charset="utf-8" src="http://d3js.org/d3.v3.min.js"></script>
    <script src="js/lib/bootstrap/bootstrap.min.js" type="text/javascript"></script>
    <script src="js/lib/mustache/mustache.js" type="text/javascript"></script>
    <script src="js/lib/chart/Chart.js" type="text/javascript"></script>
    <script src="js/lib/moment/moment.js" type="text/javascript"></script>
    <script src="js/lib/list/list.js" type="text/javascript"></script>
    <script src="js/lib/sonic/sonic.js" type="text/javascript"></script>
    <script src="js/lib/scrollto/jquery.scrollTo.js" type="text/javascript"></script>
    <script src="js/lib/easing/jquery.easing.1.3.js" type="text/javascript"></script>
    <script src="js/queries.js"></script>
    <script src="js/visualizations.js"></script>
    <script src="js/common.js"></script>
    <script>
    $(document).ready(function(){
        live.queries.initialize();
        live.visualizations.initialize();
        initPreloader();
        startPreloader(0);
    });
    </script>

    <title>Invo Live</title>
</head>

<body>
   
    <!-- title -->
    <div style="text-align:center; margin-top: 30px;">
        <div id="logo">
            <img src="img/logo.svg" alt="logosvg"/>
        </div>
    </div>

    <!-- preloader -->
    <div id="results-preloader"></div>

    <!-- content -->
    <div class="container" style="margin-bottom:20px;">
        <!-- scores -->
        <div id="results">

            <div id="results-content">
            	<section id="results-summary" class="row-fluid">
	            	<div class="span6" style="height:400px;">
	            		<canvas id="summary-stats"> </canvas>
	            	</div>
	            	<div id="summary-score" class="span6">
	            		<div class="score"> 0 </div>
	            		<div class="legend"> 
	            			<img src="img/scoreoverlay.png" alt="scov"/>
	            			<div class="text"> 100 score </div>
	            		</div>
	            	</div>
            	</section>
            	<div id="invoites">
                    <div> invoites </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <span> <a href="http://goinvo.com"> by Involution Studios</a> </span> 
            <span> - </span>
            <span> <a href="http://github.com/goinvo/InvoLive"> Code</a> </span> 
            <span> - </span>
            <span> <a  href="mailto:live-internal@goinvo.com?subject=Feedback"> Feedback </a> </span>
        </div>

    </div>

    <?php
    	include_once "includes/templates.php";
    ?>
</body>
</html>