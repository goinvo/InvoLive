var live = live || {};

live.visualizations = function () {

	var $chart, $dchart, $list;

    var initializeChart = function (data){

    	d3.selectAll('svg').remove();

    	var margin = {top: 20, right: 80, bottom: 30, left: 80},
	    width = $chart.width() - margin.left - margin.right,
	    height = 300 - margin.top - margin.bottom;

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
	    		return yd(d.user);
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

  //     initializeDchart = function(data){
  //   	var doughnutData = [];
  //   	var users = [];
  //   	// accumulate data
  //   	$.each(data, function(i){
  //   		users.push(this.user);
  //   		doughnutData.push({
  //   			"fillColor" : this.color,
  //   			value : d3.sum(this, function(d){ return d.value })
  //   		});
  //   	})
  //   	log(users);
  //   	log(doughnutData)

		// var myDoughnut = new Chart(document.getElementById("dchart").getContext("2d")).Pie(doughnutData);
	
  //   }

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

    };

    return {
        initialize: initialize,
        initializeChart : initializeChart,
    	initializeDchart : initializeDchart,
    	initializeList : initializeList
    }
}();