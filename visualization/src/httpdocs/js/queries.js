var live = live || {};

live.queries = function () {
    var url = "http://live.dev/api/";
    var $user, $eventtype, $time, $grouping;
    var compression = {
        lastday : 'hour',
        lastmonth : 'day',
        alltime : 'day'
    }

    var populateSelector = function($selector, url){
    	$.getJSON(url, function(data){
    		var content = data.message;
    		$selector.html(Mustache.render($('#option-template').html(), content));
            $selector.chosen();
    	})
    },

    query = function(){

        var users = $user.val() || 
        $user.find('option').map(function(){
           return $(this).val();
        }).get();;

        var jxhr = [];
        var result = [];

        $.each(users, function () {
            jxhr.push(
                $.getJSON(url+'measurement', {
                    user : this,
                    eventtype : $eventtype.val(),
                    time : $time.val(),
                    compression : compression[$time.val()]
                }, function(data){
                    var data = data.message;
                    // str to date
                    $.each(data, function(){
                        this.timestamp = new Date(this.timestamp);
                    });

                    result.push(data);

                })
            );
        });

        $.when.apply($, jxhr).done(function() {
            live.visualizations.initializeChart(result, $time.val());
            live.visualizations.initializeDchart(result);
        });
    },



    initialize = function (div) {

    	$user = $('#selector-user');
    	$eventtype = $('#selector-event');
    	$time = $('#selector-time');
    	$grouping = $('#selector-grouping');

    	$time.chosen({disable_search_threshold: 10});
    	$grouping.chosen({disable_search_threshold: 10});
    	
    	populateSelector($eventtype, url+'eventtype');
    	populateSelector($user, url+'user');

        setTimeout(function(){
            live.queries.query()
        },500);


        $('#button-query').click(query);


    };

    return {
        initialize: initialize,
        query : query
    }
}();