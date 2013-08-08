var live = live || {};

live.visualizations = function () {

	var $chart, $dchart, $list, $legend, $timelegend;
	var xscale;

	draw = function(data){
		xscale = d3.time.scale()
    	.domain([
		    timeranges["lastmonth"].minDate,
		    moment().toDate()
		]);


		$.each(data, function(){
			drawUser(this);
		})

	},

	drawUser = function(data){
		if($('.user').size() % 4 === 0) {
			$container.append($('#row-template').html());
		}
		$('.row-results').last().append(Mustache.render($('#entry-template').html(), data));

		$user = $('.user').last();
		$user.data(data);

		drawUserStats($user, data);

		$user.click(userClick);


	},

	drawUserStats = function($user, data){
		$stats = $user.find('canvas');

		function drawLabels(metrics, context){
			$.each(metrics, function(){
				var metric = this;
				var imageObj = new Image();
				imageObj.src = 'img/' + metric.icon;

				imageObj.onload = function() {
					context.drawImage(imageObj, metric.labelx, metric.labely, 16, 16);
				};
			});
		}

		var scores = getScores(data);

		var radarChartData = {
			labels : $.map(scores, function(val, i) { return '   ' }),
			datasets : [
				{
					fillColor : setOpacity(data.color,0.15),
					strokeColor : setOpacity(data.color,0.4),
					pointColor : setOpacity(data.color,0.4),
					pointStrokeColor : "#fff",
					data : $.map(scores, function(val, i) { return val.score })
				}
				
			]
		}

		var cvs = $stats.get(0).getContext("2d");

		var radar = new Chart(cvs).Radar(radarChartData,
			{
				scaleShowLabels : false, 
				pointLabelFontSize : 10,
				scaleOverride : true,
				scaleStartValue : 0,
				scaleSteps : 5,
				scaleStepWidth : 20,
				animationSteps : 5,
				onAnimationComplete : function(){
					drawLabels(scores, cvs);
				}
		});


	}

	userClick = function(){
		var $row = $(this).parent(),
			$user = $(this),
			$details,
			data = $(this).data(),
			user = data.user;

		$('.user').removeClass('active');
		$(this).addClass('active');

		if($('.user-details').length > 0){

			if($('.user-details').data('user') === user) {
				$('.user-details').slideUp(function(){$(this).remove()});
				return;
			} else {
				$('.user-details').slideUp(function(){
					$(this).remove();
				});
			}
		}
		$details = $details || $(Mustache.render($('#userdetails-template').html(), data)).appendTo($row.parent());
		$details.data('user', user);
		// new strip

		drawUserDetails($details, data);

		setTimeout(function(){
			$details.slideDown(300, function() { $.scrollTo($user.offset().top - 10, 300); });
		}, 200);
	}

	drawUserDetails = function($container, data){
		var details = ["Dropbox Actions", "Work Hours", "Steps"];
		$.each(details, function(){
			drawStrip($container, events[this], data);
		});
		drawLegends($container);
	}

	drawLegends = function($container){
		$rendered = $(Mustache.render($('#strip-template').html(), event)).appendTo($container);
		$strip = $rendered.find('.strip-content');

		var margin = {top: 0, right: 0, bottom: 0, left: 10};
	    width = $strip.width()- margin.left - margin.right,
	    height = $strip.height() - margin.top - margin.bottom;

		var svg = d3.select($strip.get(0)).append('svg')
		.classed('timelegend', true)
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

	drawStrip = function($container, event, data){
		eventData = [];
		$.each(data, function(){
			if($.inArray(this.eventtype, event.value) >= 0) eventData = eventData.concat(this.data);
		})

		if(eventData.length === 0) return;

		// strip
		$rendered = $(Mustache.render($('#strip-template').html(), event)).appendTo($container);

		var value = d3.sum(eventData, function(d){ return d.value });
		$rendered.find('.strip-value').text(value);

		drawStripSvg(eventData, event, $rendered.find('.strip-content'));
	},

	getScores = function(data){
		var userMetrics = [];
		for(key in metrics){
			var metric = metrics[key];
			// temporary
			if(metric.value !== undefined) {
				userMetrics.push($.extend(metric, {score : metric.value }))
			} else {
				score = 0;
				weights = 0;
				$.each(metric.submetrics, function(){
					weights += this.weight;
					score += events[this.name].score(data)*this.weight;
				});
				userMetrics.push($.extend(metric, {score : score/weights }));
			}
		}
		return userMetrics;
	},

	drawStripSvg = function(data, event, $container){

		var margin = {top: 0, right: 0, bottom: 0, left: 20},
	    width = $container.width();
	    height = $container.height();

		var svg = d3.select($container.get(0)).append('svg')
		.classed('data', true)
		.attr('width', width + margin.left + margin.right)
		.attr('height', height + margin.top + margin.bottom)
		.append("g")
	    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	    xscale.range([margin.right, $container.width()]);

	   	var rscale = d3.scale.linear().range([4,20])
	    .domain(d3.extent(data, function(d) { return d.value }));

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
    	.style('fill', event.color)
    	.style('stroke', event.color)
    	.each(function(d,i){
    		$(this).tooltip({
    			container : 'body',
    			title : Mustache.render(
    				$('#usertooltip-template').html(), { event : event.name, value : d.value }
    			),
    			html : true
    		});
    	})
	},

	// drawLegends = function (data){
	// 	$legend.html('');
	// 	$timelegend.html('');

	// 	var legendEntries = 4;
	// 	$('<div class="labl"> </div>').appendTo($legend).text(data[0].eventtype);
	// 	for(var i=0; i<=legendEntries; i++){
	// 		// get current domain
	// 		var valueDomain = i*((rscale.domain()[1] - rscale.domain()[0])/legendEntries);
	// 		var valueRange = rscale(valueDomain);

	// 		// append div and assign slice
	// 		$entry = $('<div class="entry"> </div>').appendTo($legend);
	// 		var icon = d3.select($entry.get(0)).append('svg').attr('height',$legend.height())
	// 		.attr('width',rscale.range()[1]*2 + 2);

	// 		icon.append('circle').attr('r', valueRange)
	// 		.attr('cx', icon.attr('width')/2)
	// 		.attr('cy', icon.attr('height')/2)
	// 		.style('fill', 'steelblue').style('opacity', 0.3);

	// 		$entry.append('<div class="labl">' + Math.round(rscale.invert(valueRange)/5)*5 + '</div>')
	// 	}

	// 	var margin = {top: 0, right: 0, bottom: 0, left: 10};
	//     width = $timelegend.width()- margin.left - margin.right,
	//     height = $timelegend.height() - margin.top - margin.bottom;

	// 	var svg = d3.select($timelegend.get(0)).append('svg')
	// 	.attr('width', width + margin.left + margin.right)
	// 	.attr('height', height + margin.top + margin.bottom)
	// 	.append("g")
	//     .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	//     var xAxis = d3.svg.axis()
	// 		.ticks(5).tickPadding(10)
	// 	    .scale(xscale)
	// 	    .orient("bottom");

	// 	svg.append("g")
	// 	.attr("class", "axis")
	// 	.call(xAxis);
	// },




	// draw = function(data){
	// 	$chart.html('');
		// xscale = d3.time.scale()
  //   	.domain([
		//     d3.min(data, function(c) { var min = d3.min(c, function(d) { return d.timestamp; }); return min;}),
		//     d3.max(data, function(c) { var max = moment().toDate(); return max; })
		// ]);

		// rscale = d3.scale.linear()
		// .range([4, 20])
		// .domain([
		//     0,
		//     d3.max(data, function(c) { return d3.max(c, function(d) { return d.value; }); })
		// ]);

	// 	$.each(data, function(i, user){
	// 		drawStrip(user);
	// 	})

	// 	drawLegends(data);
	// 	stopPreloader();

	// },

 //    initializeLineChart = function(data){
 //   		var datasets = [];
 //   		var labels = [];

 //   		for(var i=0; i<50; i++){
 //   			labels.push('a');
 //   		}


 //   		$.each(data, function(i, userdata){
 //   			data = [];
 //   			$.each(userdata, function(){
 //   				data.push(this.value);
 //   			})
 //   			datasets.push({
 //   				strokeColor : this.color,
 //   				fillColor : setOpacity(this.color, 0),
 //   				data : data
 //   			});
 //   		});

 //   		var data = {
 //   			labels : labels,
 //   			datasets : datasets
 //   		}
 //   		var ctx = $("#linechart").get(0).getContext("2d");
 //   		var myLinechart = new Chart(ctx).Line(data, {pointDot : false});
 //    },


    initialize = function () {
    	$container = $('#results-content');
    	$container.fadeIn(1000);
    };

    return {
        initialize: initialize,
        draw : draw
    }
}();