
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