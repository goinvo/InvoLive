var live = live || {};

live.queries = function () {
    // api url
    var url = "http://live.goinvo.com/api/";
    // user array
    var users = [];


    /*
    *   Gets avatar for specified user
    */
    getAvatar = function(name){
        for(var i=0; i<users.length; i++){
            if(users[i].name == name){
                return users[i].avatar;
            }
        }
    }

    /*
    *   Gets score data for a single or multiple users
    *   @param {array} - users for which scores have to be retrieved
    *   @param {function} - callback to be called when 
    */
    getScoreData = function(users, callback){
        currentTimerange = timeranges.lastmonth;

        $.getJSON(url+'score', {
            user : users,
            startdate : currentTimerange.start,
            enddate : currentTimerange.end
        }, function(data){
            data = data.message;
            var result = [];
            $.each(data, function(i, d){
                if(d.user === 'liveworker') return;
                d.pic = getAvatar(d.user);
                d.color = colors[i%10];
                result.push(d);
            });
            callback(result);

        });

    }

    /*
    *   Gets event data for a single or multiple users
    *   @param {array} - users for which scores have to be retrieved
    *   @param {function} - callback to be called when 
    */
    getEventData = function(user, callback){

        currentEvent = events.all;
        currentTimerange = timeranges.lastmonth;


        $.getJSON(url+'measurement', {
            user : user,
            eventtype : currentEvent.value,
            startdate : currentTimerange.start,
            enddate : currentTimerange.end,
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

            callback(data);
        });

    }

    ondataload = function(data){
        stopPreloader();
        // initialize and draw visualizations
        live.visualizations.initialize();
        live.visualizations.draw(data);
    },

    initialize = function (div) {
        // get users and query
        $.getJSON(url+'user', function(data){
            users = data.message;
            // query();
            getScoreData($.map(users, function(user, i) { return user.name }), ondataload);
        });
    };

    return {
        initialize: initialize,
        getEventData : getEventData,
        getScoreData : getScoreData,
        getAvatar : getAvatar
    }
}();