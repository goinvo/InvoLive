var live = live || {};

live.queries = function () {
    var url = "proxy.php?url=http://live.dev/api/",
    	eventtype = 'eventtype',
    	user = 'user';

    var $user, $eventtype, $time, $grouping;

    var populateSelector = function($selector, url){
    	$.getJSON(url, function(data){
    		// strip proxy status
    		var data = data.contents;
    		var content = data.message;
    		$selector.html(Mustache.render($('#option-template').html(), content));
    		$selector.chosen();
    	})
    },

    initialize = function (div) {

    	$user = $('#selector-user');
    	$eventtype = $('#selector-event');
    	$time = $('#selector-time');
    	$grouping = $('#selector-grouping');

    	$time.chosen({disable_search_threshold: 10});
    	$grouping.chosen({disable_search_threshold: 10});
    	

    	populateSelector($eventtype, url+eventtype);
    	populateSelector($user, url+user)

    	//eventtype init
    	// $.getJSON(url+eventtype, function(data){
    	// 	// strip proxy status
    	// 	var data = data.contents;
    	// 	var eventtypes = data.message;
    	// 	$eventtype.html(Mustache.render($('#option-template').html(), eventtypes));
    	// 	$eventtype.chosen();
    	// })

    	//user init
    	// $.getJSON(url+eventtype, function(data){
    	// 	// strip proxy status
    	// 	var data = data.contents;
    	// 	var eventtypes = data.message;
    	// 	$eventtype.html(Mustache.render($('#option-template').html(), eventtypes));
    	// 	$eventtype.chosen();
    	// })
    };

    return {
        initialize: initialize
    }
}();