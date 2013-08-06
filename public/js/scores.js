var live = live || {};

live.scores = function () {
	var d;

	var dropbox = function(data) {
		return Math.min(100, d3.sum(function(d){ return d.value }));
	},

	workHours = function(data){
		return Math.floor(d3.sum(function(d){ return d.value })*100/40);
	},

	steps = function(data){
		return Math.floor(d3.sum(function(d){ return d.value })*100/3000);
	},


    initialize = function () {

    };

    return {
        initialize: initialize,
        dropbox : dropbox,
        workHours : workHours,
        steps : steps
    }

}();