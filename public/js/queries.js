var live = live || {};

live.queries = function () {
    var url = "http://live.goinvo.com/api/";
    var $user, $eventtype, $time, $grouping;
    var users = [];

    getUserData = function(query, user){
        var filter = [];
        $.each(query, function(j, data){
            if(data.user == user){
                filter.push(data);
            }
        });
        return filter;
    },

    ondataload = function(data){
        stopPreloader();
        live.visualizations.initialize();
        live.visualizations.draw(data);
    },

    query = function(){

        // set current event
        currentEvent = events.all;
        currentTimerange = timeranges.lastmonth;

        startPreloader();

        // var jxhr = [];
        var result = [];

        $.getJSON(url+'measurement', {
            user : $.map(users, function(user, i) { return user.name }),
            eventtype : currentEvent.value,
            time : currentTimerange.value,
            resolution : currentTimerange.resolution
        }, function(data){
            var data = data.message;
            stopPreloader();
            
            // parse dates
            $.each(data, function(){
                $.each(this.data,function(){
                    this.timestamp = moment(this.timestamp).subtract('hours', 4).toDate();
                })
            })

            var result = [];
            $.each(users, function(i, user){
                if(user.name === 'liveworker') return;
                var userobj = getUserData(data, user.name);
                userobj.user = user.name;
                userobj.pic = user.avatar;
                userobj.color = colors[i%10];
                result.push(userobj);

            });
            ondataload(result);
        });
    },

    initialize = function (div) {

    	$user = $('#selector-user');
    	$eventtype = $('#selector-event');
    	$time = $('#selector-time');
    	$grouping = $('#selector-grouping');
    	
    	//populateSelector($eventtype, url+'eventtype', '#event-template');
        // populate eventtype 

        $.getJSON(url+'user', function(data){
            users = data.message;
            live.queries.query();
        });


        $('#button-query').click(query);


    };

    return {
        initialize: initialize,
        query : query
    }
}();