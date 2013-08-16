var live = live || {};

live.visualizations = function () {

	var $selected;
	var xscale;

	/*
	*	Populates page with visualizations
	*/
	draw = function(data){
		xscale = d3.time.scale()
    	.domain([
		    timeranges["lastmonth"].minDate,
		    moment().toDate()
		]);

    	// draw user scores 
		$.each(data, function(){
			drawUser(this);
		})

		// draw studio scores
		drawSummary(data);
	},

	/*
	*	Displays summary/studio visualization and scores
	*/
	drawSummary = function(data){

		var summary = getScores(data[0]),
			userScores = [];
		$.each(data, function(i, d){ 
			userScores.push(getScores(d))
		} );


		$.each(summary,function(i, d){
			this.score = d3.sum(userScores, function(d) { return d[i].score; })/data.length;
		});


		// studio score
		var finalScore = d3.sum(summary, function(d){ return d.score})/summary.length;
		var $score = $summary.find('.score').first();
		$score.animate({countNum : finalScore, opacity : 1}, 
			{ 	duration : 2500,
				easing : "easeOutCubic",
				step : function() {
					$score.text(Math.floor(this.countNum));
				}}
		);

		/*
		*	Radar chart for studio
		*/ 
		var radarChartData = {
			labels : $.map(summary, function(val, i) { return val.name }),
			datasets : [
				{
					fillColor : setOpacity(colors[0],0.15),
					strokeColor : setOpacity(colors[0],0.5),
					pointColor : setOpacity(colors[0],0.5),
					pointStrokeColor : "#fff",
					
					data : $.map(summary, function(val, i) { return val.score })
				}
			]
		}

		var $canvas = $summary.find('canvas').first();
		$canvas.attr('width', $canvas.parent().width() ).attr('height',$canvas.parent().width());
		var cvs = $canvas.get(0).getContext("2d");
		

		var radar = new Chart(cvs).Radar(radarChartData,
			{
				scaleShowLabels : false, 
				pointLabelFontSize : 10,
				scaleOverride : true,
				scaleStartValue : 0,
				scaleSteps : 2,
				scaleStepWidth : 50,
				scaleFontSize : 24,
				pointLabelFontSize : 12,
				pointDotRadius : 4,
				pointDotStrokeWidth : 2,
				animationSteps : 250,
				scaleLineWidth : 0.5,
				angleLineWidth : 0.5,	
				scaleLineColor : "#666",
				angleLineColor : "#666"

		});
	},

	/*
	*	Adds visualization for a single user
	*/
	drawUser = function(data){
		if($('.user').size() % 4 === 0) {
			$container.append($('#row-template').html());
		}
		$('.row-results').last().append(Mustache.render($('#entry-template').html(), data));

		$user = $('.user').last();
		$user.data(data);

		drawUserRadar($user, data);

		$user.click(function(){
			$selected = $(this);
			live.queries.getEventData($(this).data('user'), onUserClicked);
		});

	},

	drawUserRadar = function($user, data){
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
				scaleSteps : 2,
				scaleStepWidth : 50,
				animationSteps : 5,
				scaleLineWidth : 0.5,
				angleLineWidth : 0.5,	
				scaleLineColor : "#666",
				angleLineColor : "#666",
				onAnimationComplete : function(){
					drawLabels(scores, cvs);
				}
		});
	}

	onUserClicked = function(data){

		var $row = $selected.parent(),
			$user = $selected,
			$details,
			user = $selected.data('user');


		$('.user').removeClass('active');
		$selected.addClass('active');


		if($('.user-details').length > 0){
			// collapse if details already expanded
			if($('.user-details').data('user') === user) {
				$('.user-details').slideUp(function(){$(this).remove()});
				return;
			} else
			// expand details section 
			{
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

		// for each event draw a chart
		$.each(details, function(){
			drawEvent($container, events[this], data);
		});
		drawLegends($container);
	}

	drawEvent = function($container, event, data){
		// get datapoints
		// (don't draw anything if no events are available)
		eventData = [];
		$.each(data, function(){
			if($.inArray(this.eventtype, event.value) >= 0) eventData = eventData.concat(this.data);
		})
		if(eventData.length === 0) return;

		// add template for event strip
		$rendered = $(Mustache.render($('#strip-template').html(), event)).appendTo($container);

		// get cumulative # of event
		var value = d3.sum(eventData, function(d){ return d.value });
		$rendered.find('.strip-value').text(value);

		// draw event bubbles
		drawStripSvg(eventData, event, $rendered.find('.strip-content'));
	},

	/*
	*	Calculates score based on user data
	*/
	getScores = function(data){
		function findMetric(data, name){
			for(var i=0; i<data.scores.length;i++){
				if(data.scores[i].name == name){
					return data.scores[i];
				}
			}
		}

		var userMetrics = [];
		for(key in metrics){
			userMetrics.push($.extend({score : findMetric(data, key).value }, metrics[key]));
		}
		return userMetrics;
	},
	
	drawStripSvg = function(data, event, $container){

		// init dimensions
		var margin = {top: 0, right: 0, bottom: 0, left: 20},
	    width = $container.width();
	    height = $container.height();

	    // init svg
		var svg = d3.select($container.get(0)).append('svg')
		.classed('data', true)
		.attr('width', width + margin.left + margin.right)
		.attr('height', height + margin.top + margin.bottom)
		.append("g")
	    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	    xscale.range([margin.right, $container.width()]);

	    // radius scale
	   	var rscale = d3.scale.linear().range([4,20])
	    .domain(d3.extent(data, function(d) { return d.value }));

		var user = svg.append("g").classed("user",true);
	    
	    // add bubbles
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
    	// add popovers
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

    initialize = function () {
    	$container = $('#results-content');
    	$summary = $('#results-summary');
    	$container.fadeIn(1000);
    };

    return {
        initialize: initialize,
        draw : draw
    }
}();