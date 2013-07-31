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
    query = function(){

        // set current event
        currentEvent = events[$eventtype.val()];

        startPreloader();

        var users = $user.val() ||
        $user.find('option').map(function(){
           return $(this).val();
        }).get();

        var jxhr = [];
        var result = [];

        $.each(users, function (i, user) {
            jxhr.push(
                $.getJSON(url+'measurement', {
                    user : user,
                    eventtype : $eventtype.val(),
                    time : $time.val(),
                    resolution : resolution[$time.val()]
                }, function(data){

                    var data = data.message;
                    if(data.length === 0) return;
                    
                    // $.each(data, function() {log(this);log(this.value)});
                    // str to date
                    $.each(data, function(){
                        this.timestamp = moment(this.timestamp).subtract('hours', 4).toDate();
                    });
                    data.user = user,
                    data.color = colors[i%(colors.length-1)];
                    data.pic = $('#selector-user option[value="' + user + '"]').data('avatar');
                    data.eventtype = $eventtype.val();
                    result.push(data);
                })
            );
        });

        $.when.apply($, jxhr).done(function() {
            var nodata = true;
            $.each(result, function(){
                if(this.length != 0) nodata = false;
            })

            if(nodata) {
                $('#results').slideUp();
                $('#nodata').fadeIn();
            } else {
                live.visualizations.draw(result);
                $('#nodata').hide();
                $('#results').slideDown();
            }
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
            eventtypes.push({ name : events[key].value, value : key });
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