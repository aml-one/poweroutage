<!DOCTYPE html>
<html>
<head>
    <title>Power Outage Monitor - Korthi, Andros</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/style.css?<?=time()?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link href="/css/graph.css?<?=time()?>" media="all" rel="stylesheet" />
</head>
<body class="bgimg">

<div class="height100">

    <div class="cover">
        <div class="display-topright animate-right padding-small xlarge onlineIndicator">
            Online
        </div>
        <div class="display-topleft animate-left padding-small xlarge">
            Korthi, Andros
        </div>

        <div class="display-topmiddle">
            <h1 class="jumbo animate-top title_message">POWER&nbsp;OUTAGES</h1>
        </div>
        
        <div class="inside-cover">
            <div class="dateName">Today</div>
            <div class="chartContainer" style="">No data available</div>
            <div class="offline-warning-msg">The reader is OFFLINE!</div>
        </div>

        <div class="inside-cover">
            <div class="calendarView">
                <div class="calendarViewContent"></div><br />
            </div>
            <div class="buttons">
                <button id="prevBtn">Previous day</button>
                <button id="nextBtn">Next day</button>
                
                <button id="yesterdayBtn">Yesterday</button>
                <button id="todayBtn">Today</button>
                &nbsp;&nbsp;
                <div class="currentDayText">Today</div>
            </div>
            <div class="events">
                <div class="eventList"></div>
            </div>
        </div>
        <div>The device started logging at: <span class="text-yellow">2025-Oct-06</span></div>
        <div>Calendar first day: <span class="text-green">Oct-01</span> - Last day: <span class="text-amber">Sep-30</span></div>
        <div>AmL 2025.</div>
        
    </div>
</div>
<script src="/lib/jquery.min.js"></script>
<script src="/lib/canvasjs.min.js"></script>
<script src="/lib/graph.js"></script>

<script>
    function buildCalendar(){
        var list = [];

        $.get("/lib/_get_last12MonthsOutageCount.php", function(data) {
            var start_from_date = new Date(2025,09,01,0,0,0);
            $('.calendarViewContent').github_graph({
                data: JSON.parse(data),
                start_date: start_from_date,
                texts: ['power outage','power outages'],
                colors:[
                    {count:0, color:'#eeeeee'},
                    {count:1, color:'#d6e685'},
                    {count:2, color:'#8cc665'},
                    {count:3, color:'#2eb053'},
                    {count:4, color:'#97ad28'},
                    {count:5, color:'#c9b608'},
                    {count:6, color:'#de8e04'},
                    {count:7, color:'#d65b0f'},
                    {count:8, color:'#e32002'},
                    {count:10, color:'#80001e'},
                ],
                click: function(date) {
                    loadDay(date);
                },
            });
        });
    }

    function loadDay(date) {
        var today = getTodaysDate();
        var yesterday = getYesterdaysDate();
        
        if (date == today) {
            $(".currentDayText").text("Today");
            $(".dateName").text("Today");
        }
        else if (date == yesterday) {
            $(".currentDayText").text("Yesterday");
            $(".dateName").text("Yesterday");
        }
        else {
            $(".currentDayText").text(date);
            $(".dateName").text(date);
        }
        
        var dataPoints = [];
        var options =  {
            animationEnabled: true,
            theme: "light2",
            
            axisX: {
                lineColor: "#FEFEFE",
                gridColor: "#FEE",
                gridThickness: 1,
                labelFormatter: function (e) {
				    return CanvasJS.formatDate( e.value, "M/D H:mm:ss");
			    },
            },
            axisY: {
                title: "POWER LOSS     -     POWER ON",
                titleFontSize: 20,
                minimum: 0,
                maximum: 1,
                interval: 1,
                lineColor: "#EEEEEE",
                gridColor: "#FEFEFE",
            },
            toolTip: {
                shared: true,
                contentFormatter: function (e) {
                    var content = " ";
                    for (var i = 0; i < e.entries.length; i++) {
                        content += "<strong>" + e.entries[i].dataPoint.x + "</strong>";
                        content += "<br/>";
                    }
                    return content;
                }
		    },
            data: [{
                type: "line", 
                lineColor: "green",
                dataPoints: dataPoints,
                lineThickness: 2
            }]
        };

        function addData(data) {
            for (var i = 0; i < data.length; i++) {
                dataPoints.push({
                    x: new Date(data[i].time),
                    y: data[i].event
                });
            }
            $(".chartContainer").CanvasJSChart(options);
        }

        $.get("/lib/_get_entries.php", {"date": date}, function(data){
            dataPoints.length = 0;
            addData(JSON.parse(data));
        });

        $.get("/lib/_get_events.php", {"date": date}, function(data){
            if (data.length < 1)
                $(".eventList").html("<div class='noOutage'>No power outage</div>");
            else
                $(".eventList").html(data);
        });
        
    }

    function getTodaysDate() {
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); 
        var yyyy = today.getFullYear();
        
        today = yyyy + '-' + mm + '-' + dd;
        return today;
    }

    function getYesterdaysDate() {
        var date = new Date();
        date.setDate(date.getDate() - 1);

        var dd = String(date.getDate()).padStart(2, '0');
        var mm = String(date.getMonth() + 1).padStart(2, '0');
        var yyyy = date.getFullYear();
        
        date = yyyy + '-' + mm + '-' + dd;
        return date;
    }

    function getPreviousDay(posted_date) {
        if (posted_date == "Today")
            posted_date = getTodaysDate();

        if (posted_date == "Yesterday")
            posted_date = getYesterdaysDate();

        var date = new Date(posted_date);
        date.setDate(date.getDate() - 1);

        var dd = String(date.getDate()).padStart(2, '0');
        var mm = String(date.getMonth() + 1).padStart(2, '0');
        var yyyy = date.getFullYear();
        
        date = yyyy + '-' + mm + '-' + dd;
        return date;
    }

    function getNextDay(posted_date) {
        if (posted_date == "Today")
            posted_date = getTodaysDate();

        if (posted_date == "Yesterday")
            posted_date = getYesterdaysDate();

        var date = new Date(posted_date);
        date.setDate(date.getDate() + 1);

        var dd = String(date.getDate()).padStart(2, '0');
        var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = date.getFullYear();
        
        date = yyyy + '-' + mm + '-' + dd;
        return date;
    }

    $( document ).ready(function() {
        $("#todayBtn").click(function() {
            var today = getTodaysDate();
            loadDay(today);
        }); 

        $("#yesterdayBtn").click(function() {
            var yesterday = getYesterdaysDate();
            loadDay(yesterday);
        }); 

        $("#prevBtn").click(function() {
            var day = getPreviousDay($(".currentDayText").text());
            loadDay(day);
        }); 

        $("#nextBtn").click(function() {
            var day = getNextDay($(".currentDayText").text());
            loadDay(day);
        }); 

        var dataPoints = [];

        var options =  {
            animationEnabled: true,
            theme: "light2",            
            axisX: {
                lineColor: "#FEFEFE",
                gridColor: "#FEE",
                gridThickness: 1,
                labelFormatter: function (e) {
				    return CanvasJS.formatDate( e.value, "M/D H:mm:ss");
			    },
            },
            axisY: {
                title: "POWER LOSS     -     POWER ON",
                titleFontSize: 20,
                minimum: 0,
                maximum: 1,
                interval: 1,
                lineColor: "#EEEEEE",
                gridColor: "#FEFEFE",
            },
            toolTip: {
                shared: true,
                contentFormatter: function (e) {
                    var content = " ";
                    for (var i = 0; i < e.entries.length; i++) {
                        content += "<strong>" + e.entries[i].dataPoint.x + "</strong>";
                        content += "<br/>";
                    }
                    return content;
                }
		    },
            data: [{
                type: "line", 
                lineColor: "green",
                dataPoints: dataPoints,
                lineThickness: 2
            }]
        };

        function addData(data) {
            for (var i = 0; i < data.length; i++) {
                dataPoints.push({
                    x: new Date(data[i].time),
                    y: data[i].event
                });
            }
            $(".chartContainer").CanvasJSChart(options);
        }

        var updateChart = function () {
            if ($(".currentDayText").text() == "Today") {           
                $.get("/lib/_get_todays_entries.php", function(data){
                    dataPoints.length = 0;
                    addData(JSON.parse(data));
                });

                $.get("/lib/_get_today_events.php", function(data){
                    if (data.length < 1)
                        $(".eventList").html("<div class='noOutage'>No power outage</div>");
                    else
                        $(".eventList").html(data);
                });
            }
        };

        var checkOnline = function () {
            $.get("/lib/_isDeviceIsOnline.php", function(data){
                if (data=="offline") {
                    $(".onlineIndicator").css('color', 'red');
                    $(".onlineIndicator").text("Offline");
                    $(".offline-warning-msg").css('display', 'block');
                }
                else {
                    $(".onlineIndicator").css('color', 'lightgreen');
                    $(".onlineIndicator").text("Online");
                    $(".offline-warning-msg").css('display', 'none');
                }
            });
        };

        updateChart(); 
        checkOnline();
        buildCalendar();

        setInterval(function(){ updateChart() }, 60000); 
        setInterval(function(){ buildCalendar() }, 60000); 
        setInterval(function(){ checkOnline() }, 5000); 
    });
</script>

</body>
</html>
