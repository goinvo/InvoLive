var live = live || {};

live.scores = function () {
	var d;

	var getEventData = function(event, data){
		eventData = [];
		$.each(data, function(){
			if($.inArray(this.eventtype, event.value) >= 0) eventData = eventData.concat(this.data);
		})
		if (eventData.length === 0 ) return null;
		return eventData;
	},

	dropbox = function(data) {
		var eventData = getEventData(events['Dropbox Actions'], data);
		if(eventData === null) return 0;
		return Math.min(100, d3.sum(eventData, function(d){ return d.value }));
	},

	workHours = function(data){
		var eventData = getEventData(events['Work Hours'], data);
		if(eventData === null) return 0;
		return Math.min(100, d3.sum(eventData, function(d){ return d.value })*100/200);
	},

	steps = function(data){
		var eventData = getEventData(events['Steps'], data);
		if(eventData === null) return 50;
		return Math.max(Math.min(100,d3.sum(eventData, function(d){ return d.value })*100/200000), 50);
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