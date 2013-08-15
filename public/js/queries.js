var live = live || {};

live.queries = function () {
    // api url
    var url = "http://live.dev/api/";
    // user array
    var users = [];

    /*
    *   Filters results for user
    */
    getUserData = function(data, user){
        var filter = [];
        $.each(data, function(j, d){
            if(d.user == user){
                filter.push(d);
            }
        });
        return filter;
    },

    getAvatar = function(name){
        for(var i=0; i<users.length; i++){
            if(users[i].name == name){
                return users[i].avatar;
            }
        }
    }

    queryScores = function(callback){

        currentTimerange = timeranges.lastmonth;

        $.getJSON(url+'score', {
            user : $.map(users, function(user, i) { return user.name }),
            startdate : currentTimerange.startdate,
            enddate : currentTimerange.enddate
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

    queryEvents = function(user, callback){

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

    /*
    *   Fetches and processes user data
    */
    query = function(){

        // set current event

        // ask for all events over last month
        currentEvent = events.all;
        currentTimerange = timeranges.lastmonth;

        startPreloader();

        // var jxhr = [];
        var result = [];

        $.getJSON(url+'measurement', {
            user : $.map(users, function(user, i) { return user.name }),
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
            queryScores(ondataload);
        });
    };

    return {
        initialize: initialize,
        query : query,
        queryEvents : queryEvents,
        getAvatar : getAvatar
    }
}();