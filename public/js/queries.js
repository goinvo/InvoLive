var live = live || {};

live.queries = function () {
    var url = "http://live.goinvo.com/api/";
    var $user, $eventtype, $time, $grouping;
    var resolution = {
        "lastday" : 'hour',
        "lastmonth" : 'day',
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
                    log(user);
                    
                    $.each(data, function() {log(this);log(this.value)});
                    // str to date
                    $.each(data, function(){
                        this.timestamp = moment(this.timestamp).subtract('hours', 4).toDate();
                    });
                    log(data);
                    data.user = user,
                    data.color = colors[i];
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
                live.visualizations.initializeChart(result, $time.val());
                live.visualizations.initializeDchart(result);
                live.visualizations.initializeList(result);
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
    	
    	populateSelector($eventtype, url+'eventtype', '#event-template');
    	populateSelector($user, url+'user', '#user-template');

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