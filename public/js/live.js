
/*
*	Global variables
*/
var colors = d3.scale.category10().range();
var $preloader, preloader;
var currentEvent;

var events = {
	"Dropbox Actions" : {
		value : 'Dropbox actions',
		click : null
	},
	"Actual work hours" : {
		value : 'Work hours',
		click : live.visualizations.staffplanExpansion
	},
	"Steps" : {
		value : 'Steps',
		click : null
	}
}


/*
*	Helper functions
*/

function initPreloader(){
	$preloader = $('#results-preloader');
	var height = 50;
	var width = $preloader.width();

	preloader = new Sonic({

	width: 200,
		height: 200,

		stepsPerFrame: 1.5,
		trailLength: 1,
		pointDistance: .125,

		strokeColor: '#425f8e',

		fps: 5,

		setup: function() {
			this._.lineWidth = 10;
		},
		step: function(point, index) {
			var cx = 100,
				cy = 100,
				_ = this._,
				angle = (Math.PI/180) * (point.progress * 360),
				innerRadius = 15;

			_.beginPath();
			_.moveTo(point.x, point.y);
			_.lineTo(
				(Math.cos(angle) * innerRadius) + cx,
				(Math.sin(angle) * innerRadius) + cy
			);
			_.closePath();
			_.stroke();

		},
		path: [
			['arc', 100, 100, 70, 0, 360]
		]


	});

	$preloader.append(preloader.canvas);
	
}

function startPreloader(duration){
	preloader.play();
	$preloader.fadeIn(duration);
	$('#results-content').animate({opacity : 0.05}, 200);
}

function stopPreloader(){
	preloader.stop();
	$preloader.fadeOut(100);
	setTimeout(function(){
		$('#results-content').animate({opacity : 1}, 200);
	}, 300);
}

function log(msg){
	console.log(msg);
}

function date_sort_asc (date1, date2) {
    if (date1.timestamp > date2.timestamp) return 1;
    if (date1.timestamp < date2.timestamp) return -1;
    return 0;
};

function hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function mixrgb(rgb1, rgb2, p) {
    return {
        r: Math.round(p * rgb1.r + (1 - p) * rgb2.r),
        g: Math.round(p * rgb1.g + (1 - p) * rgb2.g),
        b: Math.round(p * rgb1.b + (1 - p) * rgb2.b)
    };
}

function setOpacity(hex, opacity){
	var rgb = hexToRgb(hex);
	return 'rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b + ',' + opacity + ')';
}