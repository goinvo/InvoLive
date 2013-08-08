var live = live || {};

live.scores = function () {

	var getEventData = function(event, data){
		eventData = [];
		$.each(data, function(){
			if($.inArray(this.eventtype, event.value) >= 0) eventData = eventData.concat(this.data);
		})
		return eventData;
	},

	dropbox = function(data) {
		//scale
		var scoreScale = d3.scale.linear().clamp(true).range([30,100])
		.domain([0, 100]);

		var eventData = getEventData(events['Dropbox Actions'], data);
		return scoreScale(d3.sum(eventData, function(d){ return d.value }));
	},

	workHours = function(data){
		//not linear (scales difference from ideal value of 40*4 = 160)
		var scoreScale = d3.scale.linear().clamp(true).range([100,30])
		.domain([0, 80]);

		var eventData = getEventData(events['Work Hours'], data);
		return scoreScale( Math.abs(160 - d3.sum(eventData, function(d){ return d.value })));
	},

	steps = function(data){
		//scale
		var scoreScale = d3.scale.linear().clamp(true).range([30,100])
		.domain([0, 250000]);

		var eventData = getEventData(events['Steps'], data);
		return scoreScale(d3.sum(eventData, function(d){ return d.value }));
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