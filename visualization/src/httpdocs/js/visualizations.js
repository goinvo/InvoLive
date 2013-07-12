var live = live || {};

live.visualizations = function () {

    var initializeChart = function (){
    	var $chart = $('#chart');
		var lineChartData = {
					labels : ["January","February","March","April","May","June","July"],
					datasets : [
						{
							fillColor : "rgba(220,220,220,0.5)",
							strokeColor : "rgba(220,220,220,1)",
							pointColor : "rgba(220,220,220,1)",
							pointStrokeColor : "#fff",
							data : [65,59,90,81,56,55,40]
						},
						{
							fillColor : "rgba(151,187,205,0.5)",
							strokeColor : "rgba(151,187,205,1)",
							pointColor : "rgba(151,187,205,1)",
							pointStrokeColor : "#fff",
							data : [28,48,40,19,96,27,100]
						}
					]
					
				}

		var myLine = new Chart(document.getElementById("chart").getContext("2d")).Line(lineChartData);
    },

    initializeDchart = function(){
    			var doughnutData = [
				{
					value: 30,
					color:"#F7464A"
				},
				{
					value : 50,
					color : "#46BFBD"
				},
				{
					value : 100,
					color : "#FDB45C"
				},
				{
					value : 40,
					color : "#949FB1"
				},
				{
					value : 120,
					color : "#4D5360"
				}
			
			];

		var myDoughnut = new Chart(document.getElementById("dchart").getContext("2d")).Doughnut(doughnutData);
	
    }



    var initialize = function () {
    	initializeChart();
    	initializeDchart();
    };

    return {
        initialize: initialize
    }
}();