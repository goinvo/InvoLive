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

        log($eventtype.val());

        $.each(users, function (i, user) {
            jxhr.push(
                $.getJSON(url+'measurement', {
                    user : user,
                    eventtype : $eventtype.val(),
                    time : $time.val(),
                    compression : compression[$time.val()]
                }, function(data){
                    var data = data.message;
                    // str to date
                    $.each(data, function(){
                        this.timestamp = new Date(this.timestamp);
                    });
                    data.user = user;
                    data.color = colors[i];
                    data.pic = getuserpic(user);
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

    getuserpic = function(name){
        return url+'user/image?user='+name;
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
        query : query,
        getuserpic : getuserpic
    }
}();