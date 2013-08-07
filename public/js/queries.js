var live = live || {};

live.queries = function () {
    var url = "http://live.dev/api/";
    var $user, $eventtype, $time, $grouping;
    var resolution = {
        "lastday" : 'hour',
        "lastmonth" : 'day',
        "lastyear" : 'day',
        "alltime" : 'day'
    }

    var populateSelector = function($selector, url, template){
    	$.getJSON(url, function(data){
    		var content = data.message;
    		$selector.html(Mustache.render($(template).html(), content));
            $selector.chosen()
    	})
    },

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
        currentEvent = events[$eventtype.val()];

        startPreloader();

        var users = $user.val() ||
        $user.find('option').map(function(){
           return $(this).val();
        }).get();

        // var jxhr = [];
        var result = [];

        $.getJSON(url+'measurement', {
            user : users,
            eventtype : events["All"].value,
            time : $time.val(),
            resolution : resolution[$time.val()]
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
                var userobj = getUserData(data, user);
                userobj.user = user;
                userobj.pic = $('#selector-user option[value="' + user + '"]').data('avatar');
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

    	$time.chosen({disable_search_threshold: 10});
    	$grouping.chosen({disable_search_threshold: 10});
    	
    	//populateSelector($eventtype, url+'eventtype', '#event-template');
        // populate eventtype 
        var eventtypes = [];
        for( key in events ){
            eventtypes.push({ name : key, value : key });
        }
        $eventtype.html(Mustache.render($('#event-template').html(), eventtypes));
        $eventtype.chosen()

    	populateSelector($user, url+'user', '#user-template');

        setTimeout(function(){
            live.queries.query();
        },500);


        $('#button-query').click(query);


    };

    return {
        initialize: initialize,
        query : query
    }
}();