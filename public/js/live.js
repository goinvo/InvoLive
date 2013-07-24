
/*
*	Global variables
*/
var colors = d3.scale.category10().range();
var $preloader, preloader;

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
	stepsPerFrame: 2,
	trailLength: 1,
	pointDistance: .008,
	fps: 30,


		fillColor: '#1f77b4',

		step: function(point, index) {
			
			this._.beginPath();
			this._.moveTo(point.x, point.y);
			this._.arc(point.x, point.y, index * 7, 0, Math.PI*2, false);
			this._.closePath();
			this._.fill();

		},

		path: [
			['arc', 100, 100, 80, 0, 360]
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
	$preloader.fadeOut();
	$('#results-content').animate({opacity : 1}, 200);
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