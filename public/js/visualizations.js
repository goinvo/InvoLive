var live = live || {};

live.visualizations = function () {

	var $chart, $dchart, $list, $legend;

	var initializeLegend = function (scale){
		var legendEntries = 4;
		$legend.find('.labl').text('Dropbox Activity');
		for(var i=0; i<=legendEntries; i++){
			// get current domain
			var valueDomain = i*((scale.domain()[1] - scale.domain()[0])/legendEntries);
			var valueRange = scale(valueDomain);

			// append div and assign slice
			$entry = $('<div class="entry"> </div>').appendTo($legend);
			var icon = d3.select($entry.get(0)).append('svg').attr('height',$legend.height())
			.attr('width',scale.range()[1]*2 + 2);

			icon.append('circle').attr('r', valueRange)
			.attr('cx', icon.attr('width')/2)
			.attr('cy', icon.attr('height')/2)
			.style('fill', 'steelblue').style('opacity', 0.3);

			$entry.append('<div class="labl">' + Math.round(scale.invert(valueRange)/5)*5 + '</div>')
		}
	}

    var initializeChart = function (data){

    	d3.selectAll('svg').remove();

    	var margin = {top: 20, right: 0, bottom: 30, left: 90},
	    width = $chart.width() - margin.left - margin.right,
	    height = 350 - margin.top - margin.bottom;

	    var svg = d3.select($chart.get(0)).append('svg')
	    .attr('id', 'chart')
	    .attr("width", width + margin.left + margin.right)
	    .attr("height", height + margin.top + margin.bottom)
	  	.append("g")
	    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	    var x = d3.time.scale()
    	.range([0, width]);

		var r = d3.scale.linear()
		    .range([4, 20]);

		var yd = d3.scale.ordinal()
			.rangePoints([height - 20, 0]);

		var xAxis = d3.svg.axis()
			.ticks(5)
		    .scale(x)
		    .orient("bottom");

		var yAxis = d3.svg.axis()
			.ticks(10)
		    .scale(yd)
		    .tickPadding(20)
		    .orient("left");

		var line = d3.svg.line()
	    // .interpolate("monotone")
	    .x(function(d) { return x(d.timestamp); })
	    .y(function(d) { return y(d.value); });

		x.domain([
		    d3.min(data, function(c) { var min = d3.min(c, function(d) { return d.timestamp; }); return min;}),
		    d3.max(data, function(c) { var max = moment().toDate(); return max; })
		]);

		yd.domain(data.map(function(d){
			return d.user
		}))

		r.domain([
		    0,
		    d3.max(data, function(c) { return d3.max(c, function(d) { return d.value; }); })
		]);

		initializeLegend(r);

		svg.append("g")
		.attr("class", "axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis);

		svg.append("g")
		.attr("class", "axis y")
		.call(yAxis);

		var users = svg.selectAll(".user")
	    .data(data)
	    .enter().append("g")
	    .classed("user",true);

	    users.each(function(){
	    	var data = d3.select(this).datum();
	    	d3.select(this).selectAll('circle')
	    	.data(data)
	    	.enter().append('circle')
	    	.attr('r', function(d){
	    		return r(d.value);
	    	})
	    	.attr('cx', function(d){
	    		return x(d.timestamp);
	    	})
	    	.attr('cy', function(d){
	    		return yd(data.user);
	    	})
	    	.style('fill', data.color)
	    	.style('opacity', 0.5);

	    });

	    stopPreloader();

	    // users.append("path")
     //  .attr("class", "line")
     //  .attr("d", function(d) { return line(d); })
     //  .style("stroke", function(d){return d.color});


    },

    initializeLineChart = function(data){
   		var datasets = [];
   		var labels = [];

   		for(var i=0; i<50; i++){
   			labels.push('a');
   		}


   		$.each(data, function(i, userdata){
   			data = [];
   			$.each(userdata, function(){
   				data.push(this.value);
   			})
   			datasets.push({
   				strokeColor : this.color,
   				fillColor : setOpacity(this.color, 0),
   				data : data
   			});
   		});

   		var data = {
   			labels : labels,
   			datasets : datasets
   		}
   		var ctx = $("#linechart").get(0).getContext("2d");
   		var myLinechart = new Chart(ctx).Line(data, {pointDot : false});

    },

    initializeDchart = function(data){
    	var doughnutData = [];
    	// accumulate data
    	$.each(data, function(i){
    		doughnutData.push({
    			color : this.color,
    			value : d3.sum(this, function(d){ return d.value })
    		});
    	})

		var myDoughnut = new Chart(document.getElementById("dchart").getContext("2d")).Doughnut(doughnutData);
	
    }

    initializeList = function(data){
    	$list.html(Mustache.render($('#table-template').html(), data[0]));
    	$listBody = $list.find('tbody');

    	$.each(data, function(i){
    		this.lastEvent = (this.length === 0) ? '--' : moment(this[this.length-1].timestamp).fromNow();
    		this.total = d3.sum(this, function(d){ return d.value });
    		$rendered = $(Mustache.render($('#row-template').html(), this)).appendTo($listBody);
    	});

    	var options = {
		    valueNames: [ 'user', 'actions' ]
		};
    	var list = new List('user-list-container', options);
    	list.sort('actions');

    }



    var initialize = function () {
    	$chart = $('#chart-container');
    	$list = $('#user-list-container');
    	$legend = $('#chart-legend');

    };

    return {
        initialize: initialize,
        initializeChart : initializeChart,
    	initializeDchart : initializeDchart,
    	initializeList : initializeList
    }
}();