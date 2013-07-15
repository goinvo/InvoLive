var live = live || {};

live.visualizations = function () {

	var colors = d3.scale.category20().range();

    var initializeChart = function (data){
    	var $container = $('#chart-container');

    	var margin = {top: 20, right: 80, bottom: 30, left: 50},
	    width = $container.width() - margin.left - margin.right,
	    height = 300 - margin.top - margin.bottom;

	    var svg = d3.select($container.get(0)).append('svg')
	    .attr('id', 'chart')
	    .attr("width", width + margin.left + margin.right)
	    .attr("height", height + margin.top + margin.bottom)
	  	.append("g")
	    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	    var x = d3.time.scale()
    	.range([0, width]);

		var y = d3.scale.linear()
		    .range([height, 0]);

		var color = d3.scale.category10();

		var xAxis = d3.svg.axis()
		    .scale(x)
		    .orient("bottom");

		var yAxis = d3.svg.axis()
		    .scale(y)
		    .orient("left");

		var line = d3.svg.line()
	    .interpolate("basis")
	    .x(function(d) { log(x(d.timestamp)); return x(d.timestamp); })
	    .y(function(d) { return y(d.value); });

		x.domain([
		    d3.min(data, function(c) { return d3.min(c, function(d) { return d.timestamp; }); }),
		    d3.max(data, function(c) { return d3.max(c, function(d) { return d.timestamp; }); })
		]);

		y.domain([
		    d3.min(data, function(c) { return d3.min(c, function(d) { return d.value; }); }),
		    d3.max(data, function(c) { return d3.max(c, function(d) { return d.value; }); })
		]);

		svg.append("g")
		.attr("class", "axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis);

		svg.append("g")
		.attr("class", "axis")
		.call(yAxis);

		var users = svg.selectAll(".user")
	    .data(data)
	    .enter().append("g")
	    .attr("class", "user");

	    users.append("path")
      .attr("class", "line")
      .attr("d", function(d) { return line(d); });


    },

    initializeDchart = function(data){
    	var doughnutData = [];
    	// accumulate data
    	$.each(data, function(i){
    		doughnutData.push({
    			color : colors[i],
    			value : d3.sum(this, function(d){ return d.value })
    		});
    	})

		var myDoughnut = new Chart(document.getElementById("dchart").getContext("2d")).Doughnut(doughnutData);
	
    }



    var initialize = function () {
    };

    return {
        initialize: initialize,
        initializeChart : initializeChart,
    	initializeDchart : initializeDchart
    }
}();