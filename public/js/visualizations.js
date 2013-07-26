var live = live || {};

live.visualizations = function () {

	var $chart, $dchart, $list, $legend, $timelegend;
	var rscale, xscale;

	var drawLegends = function (data){
		$legend.html('');
		$timelegend.html('');

		var legendEntries = 4;
		$('<div class="labl"> </div>').appendTo($legend).text(data[0].eventtype);
		for(var i=0; i<=legendEntries; i++){
			// get current domain
			var valueDomain = i*((rscale.domain()[1] - rscale.domain()[0])/legendEntries);
			var valueRange = rscale(valueDomain);

			// append div and assign slice
			$entry = $('<div class="entry"> </div>').appendTo($legend);
			var icon = d3.select($entry.get(0)).append('svg').attr('height',$legend.height())
			.attr('width',rscale.range()[1]*2 + 2);

			icon.append('circle').attr('r', valueRange)
			.attr('cx', icon.attr('width')/2)
			.attr('cy', icon.attr('height')/2)
			.style('fill', 'steelblue').style('opacity', 0.3);

			$entry.append('<div class="labl">' + Math.round(rscale.invert(valueRange)/5)*5 + '</div>')
		}

		var margin = {top: 0, right: 0, bottom: 0, left: 10};
	    width = $timelegend.width()- margin.left - margin.right,
	    height = $timelegend.height() - margin.top - margin.bottom;

		var svg = d3.select($timelegend.get(0)).append('svg')
		.attr('width', width + margin.left + margin.right)
		.attr('height', height + margin.top + margin.bottom)
		.append("g")
	    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	    var xAxis = d3.svg.axis()
			.ticks(5).tickPadding(10)
		    .scale(xscale)
		    .orient("bottom");

		svg.append("g")
		.attr("class", "axis")
		.call(xAxis);

	},

	staffplanExpansion = function(){
		// if($(this).data('open') === false) {
		// 	$dropdown = $('<div class="subsection" style="display:none"></div>').appendTo($(this));
		// 	$dropdown.slideDown();
		// 	$(this).data('open', true);
		// } else {
		// 	$(this).find('.subsection').slideUp();
		// 	$(this).data('open', false);
		// }
		// $dropdown = $('<div class="subsection" style="display:none"></div>').appendTo($(this));
		// $dropdown.slideDown();
	}

	drawStripSvg = function(data, $container){
		var margin = {top: 0, right: 0, bottom: 0, left: 20},
	    width = $container.width()- margin.left - margin.right,
	    height = $container.height() - margin.top - margin.bottom;

		var svg = d3.select($container.get(0)).append('svg')
		.attr('width', width + margin.left + margin.right)
		.attr('height', height + margin.top + margin.bottom)
		.append("g")
	    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	    xscale.range([0, $container.width()]);

		var user = svg.append("g").classed("user",true);
	    user.selectAll('circle')
    	.data(data)
		.enter().append('circle')
    	.attr('r', function(d){
    		return rscale(d.value);
    	})
    	.attr('cx', function(d){
    		return xscale(d.timestamp);
    	})
    	.attr('cy', $container.height()/2)
    	.style('fill', data.color)
    	.style('fill-opacity', 0.3)
    	.style('stroke', data.color)
    	.style('stroke-opacity',0.45);



	},

	drawStripInfo = function(data, $container){
		$container.find('.strip-icon');
		var value = d3.sum(data, function(d){ return d.value });
		$container.find('.strip-value').text(value);
	},

	drawStrip = function(data){
		$rendered = $(Mustache.render($('#strip-template').html(), data)).appendTo($chart);
		drawStripInfo(data, $rendered.find('.strip-info'));
		drawStripSvg(data, $rendered.find('.strip-content'));

		$rendered.data(data);
		$rendered.click(currentEvent.click);
	},

	draw = function(data){
		$chart.html('');
		xscale = d3.time.scale()
    	.domain([
		    d3.min(data, function(c) { var min = d3.min(c, function(d) { return d.timestamp; }); return min;}),
		    d3.max(data, function(c) { var max = moment().toDate(); return max; })
		]);

		rscale = d3.scale.linear()
		.range([4, 20])
		.domain([
		    0,
		    d3.max(data, function(c) { return d3.max(c, function(d) { return d.value; }); })
		]);



		$.each(data, function(i, user){
			drawStrip(user);
		})

		drawLegends(data);
		stopPreloader();

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


    initialize = function () {
    	$chart = $('#chart-container');
    	$list = $('#user-list-container');
    	$legend = $('#chart-legend');
    	$timelegend = $('#chart-timelegend');

    };

    return {
        initialize: initialize,
        draw : draw,
        staffplanExpansion : staffplanExpansion
    }
}();