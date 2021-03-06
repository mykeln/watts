<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Watts</title>

<meta name="viewport" content = "width = device-width">
<meta name="viewport" content = "initial-scale = 1.0">
<meta name="viewport" content = "user-scalable = no">

<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<link rel="apple-touch-startup-image" href="startup.png" />

<link rel="apple-touch-icon-precomposed" href="touch-icon-iphone.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="touch-icon-ipad.png" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="touch-icon-iphone4.png" />

<link rel="shortcut icon" href="touch-icon-iphone.png">

<!-- styles -->
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.8.2r1/build/reset/reset-min.css">
<style type="text/css">
  body,html{background:#33262e;}
  h1{background:#733230;text-align:center;color:#fff;font:Bold 30px/80px "Helvetica Neue",helvetica,arial,sans-serif;}
  h2{background:#67B2A6;border-radius:20px;color:#FFFFFF;font:bold 14px/17px "Helvetica Neue",helvetica,arial,sans-serif;margin:-27px auto 0;padding:0;position:relative;text-align:center;text-shadow:2px 2px 0 #45867A;top:10px;width:100px;z-index:2;padding:5px;}
  h4{color:#ccc;text-align:center;font:Bold 25px/28px "Helvetica Neue",helvetica,arial,sans-serif;padding:5px;}
  h4 a{color:red;text-decoration:none;margin-left:10px;}
  h5{color:#fff;text-align:center;font:normal 18px/21px "Helvetica Neue",helvetica,arial,sans-serif;position:absolute;bottom:10px;}
  p{padding:20px;font:Normal 18px/21px "Helvetica Neue",helvetica,arial,sans-serif;color:#fff;}
  .warmup{background:#ff4242;}
  .workout{background:#6cc792;}
  .cooldown{background:#33262E;}
  #tp_form{background:url(watts-logo.png) center 10px no-repeat;padding:100px 20px 20px 20px;margin:0px auto;width:280px;}
  fieldset{margin-bottom:10px;}
  input{background:#fff;border:1px solid #ccc;color:#444;font:Normal 14px/17px "Helvetica Neue",helvetica,arial,sans-serif;border-radius:5px;padding:10px;width:260px;margin:10px auto 0px auto;}
  #tp_submit{cursor:pointer;background:#67B2A6;border:1px solid #45867A;color:#fff;font-weight:bold;width:280px;-webkit-appearance: none;}
  #tp_submit:hover{background:#45867A;}
  #workouts,#tp_form{display:none;-webkit-overflow-scrolling:touch;}
  #loading_message{width:280px;border-radius: 0px 0px 5px 5px;display:none;position:absolute;top:0px;left:20px;background-color:#fff;text-align:center;font:Bold 20px/50px "Helvetica Neue",helvetica,arial,sans-serif;color:#444;}
</style>
    
<style type="text/css" media="only screen and (-webkit-min-device-pixel-ratio:2)">
  #tp_form{background:url(watts-logo-retina.png) center 10px no-repeat;background-size:300px 100px;}
</style>

<!-- scripts -->
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="date.js"></script>
<script type="text/javascript" src="bookmark_bubble.js"></script>

<!-- core code -->
<script type="text/javascript">  
$(function(){
    
  window.scrollTo(0, 0);
  
/*
////////////////////////////////////////
////////////////////////////////////////
////////////////////////////////////////
UTILITY FUNCTIONS
////////////////////////////////////////
*/

var xmlDateToJavascriptDate = function(xmlDate) {
  // It's times like these you wish Javascript supported multiline regex specs
  var re = /^([0-9]{4,})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})(\.[0-9]+)?(Z|([+-])([0-9]{2}):([0-9]{2}))?$/;
  var match = xmlDate.match(re);
  if (!match)
    return null;

  var all = match[0];
  var year = match[1];  var month = match[2];  var day = match[3];
  var hour = match[4];  var minute = match[5]; var second = match[6];
  var milli = match[7]; 
  var z_or_offset = match[8];  var offset_sign = match[9]; 
  var offset_hour = match[10]; var offset_minute = match[11];

  if (offset_sign) { // ended with +xx:xx or -xx:xx as opposed to Z or nothing
    var direction = (offset_sign == "+" ? 1 : -1);
    hour =   parseInt(hour)   + parseInt(offset_hour)   * direction;
    minute = parseInt(minute) + parseInt(offset_minute) * direction;
  }
  var utcDate = Date.UTC(year, month, day, hour, minute, second, (milli || 0));
  return new Date(utcDate);
}  
function setCookie(name,value,days) {
  if (days) {
    var date = new Date();
    date.setTime(date.getTime()+(days*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
  }
  else var expires = "";
  document.cookie = name+"="+value+expires+"; path=/";
}
function getCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}
function deleteCookie(name) {
  setCookie(name,"",-1);
}

function toggleBackground(state){
  if(state == false){
    // removing the background on the body and html
    $('body').css('background-color', '#efefef');
    $('html').css('background-color', '#efefef');
  } else {
    $('body').css('background-color', '#33262e');
    $('html').css('background-color', '#33262e');
  }
}


/*
////////////////////////////////////////
////////////////////////////////////////
////////////////////////////////////////
CORE CODE
////////////////////////////////////////
*/

// getting cookie values
var nameSet = getCookie('tp_username');
var passSet = getCookie('tp_password');

// getting today's date for query AND for displaying
var todaysDateRaw = new Date();
var todaysDate = todaysDateRaw.toString('MM-dd-yy');

// checking if cookie has been set. if it has, show the workout. otherwise, show the form.
if((nameSet != null) && (passSet != null)) {
  queryTrainingPeaks(nameSet,passSet);
  console.log("Cookie detected for user: " + nameSet);
} else {
  showForm();
  console.log("No cookie detected. Showing form");
}

function errorWorkout(message) {
  console.log(message);
  return false;
}

// function for parsing the xml data and synthesizing it on the presentation layer
function showWorkout(xml){
  var workoutTitle;
  var workoutTime;
  var warmupText;
  var workoutText;
  var workoutCoach;
  var cooldownText;
  
  // looking for a workout object
  var Workouts = $(xml).find("Workout");
  
  // if a workout object exists, process it
  if (Workouts[0]) {
    Workouts.each(function() {
      console.log("Showing workout for " + todaysDate);

      // grabbing the workout title
      workoutTitle = $(this).find("Title").text();
    
      // if the workout title is empty, name a default one
      if (workoutTitle == "") {
        workoutTitle = "Today's Workout"
      }
    
      // grabbing the day, converting, stripping out weirdness
      var workoutDayRaw = $(this).find("WorkoutDay").text();
      var convertingToParse = xmlDateToJavascriptDate(workoutDayRaw);
      var rawDateObject = new Date(convertingToParse);
      var workoutDay = rawDateObject.toString('MM-dd');
    
      // grabbing the workout duration, converting to minutes
      var workoutTimeRaw = $(this).find("PlannedTimeTotalInSeconds").text();
      workoutTime = workoutTimeRaw/60;
    
      // grabbing the workout description(s)
      var workoutDesc = $(this).find("Description").text();
      workoutCoach = $(this).find("CoachComments").text();      
    
      // if warmup exists, add it to the description
      if (workoutDesc.indexOf('WU:') != -1) {      
        // parsing out warmup text from description
        var workoutTextWuRaw = workoutDesc.split("WU:");
        var workoutTextWu= workoutTextWuRaw[1].split("MS:");
        warmupText = workoutTextWu[0];
      } else {
        warmupText = "No warm up."
      }
    
      // if workout exists in a structure, split it, otherwise, just show the full description
      if (workoutDesc.indexOf('MS:') != -1) {      
        // parsing out workout text from description
        var workoutTextWoRaw = workoutDesc.split("MS:");
        var workoutTextWo= workoutTextWoRaw[1].split("CD:");
        workoutText = workoutTextWo[0];
      } else {
        workoutText = workoutDesc;
      }
    
      if (workoutDesc.indexOf('CD:') != -1) {      
        // parsing out cool down text from description
        var workoutTextCdRaw = workoutDesc.split("CD:");
        var workoutTextCd= workoutTextCdRaw[1].split("CD:");
        cooldownText = workoutTextCd[0];
      } else {
        cooldownText = "No cool down."
      }
      
      presentData(workoutTitle, workoutTime, warmupText, workoutText, workoutCoach, cooldownText, todaysDate);

    });
  } else { // no workout found
    console.log("No workout for " + todaysDate);
    
    workoutTitle = "No workout today";
    workoutTime = 0;
    warmupText = "No warmup.";
    workoutText = "No workout.";
    workoutCoach = "";
    cooldownText = "No cool down.";
    
    presentData(workoutTitle, workoutTime, warmupText, workoutText, workoutCoach, cooldownText, todaysDate);
    
  }
}

function presentData(workoutTitle, workoutTime, warmupText, workoutText, workoutCoach, cooldownText, todaysDate) {      
  // appending data to the presentation
  $("#workoutresult h1").append(workoutTitle);
  $("#workoutresult h2").append(workoutTime + " minutes");
  $("#workoutresult .warmup").append(warmupText);
  $("#workoutresult .workout").append(workoutText + workoutCoach);
  $("#workoutresult .cooldown").append(cooldownText);      

  // appending the date at the very bottom of the presentation
  $("h4 span").html(todaysDate);

  // removing the background on the body and html
  toggleBackground(false);
  
  // hiding the form, showing the workout
  $("#workouts").show();
  $("#tp_form").hide();
  
  $('#loading_message').slideUp('fast');
}
  
function queryTrainingPeaks(username,password){
  
  // debug dates
  // todaysDate = '08/01/11';
  
  // detecting if inside phonegap to not trigger proxy php
  var proxySet;
  
  if(window.PhoneGap){
    proxySet = "http://www.trainingpeaks.com/tpwebservices/service.asmx/GetWorkoutsForAthlete";
  } else {
    proxySet = "proxy.php";
  }
  
  // path to the json we're pulling
  var logUrl = proxySet + "?username=" + username + "&password=" + password + "&startDate=" + todaysDate + "&endDate=" + todaysDate;

  // grabbing xml and parsing
  $.ajax({
    type: "GET",
    url: logUrl,
    dataType: "xml",
    success: checkData,
    error: function(){ errorWorkout("Couldn't grab!"); }
  });
}

function checkData(xml){
  // [sic] parseRerror
  var parseError = $(xml).find("parsererror");
  
  // if the username/password was invalid, send back
  if (parseError.length == 0) {
    showWorkout(xml);
  } else {
    alert("The username or password was wrong.");
    $('#loading_message').slideUp('fast');
    clearUser();
  }
}

// showing the form, making sure workouts are hidden
function showForm(){
  $("#workouts").hide();
  $("#tp_form").show();
}

// click handler. assigns cookie, passes cookie variables to the queryTrainingPeaks function
$('#tp_submit').click(function() {
  if((!$('[name=username]').val() || (!$('[name=password]').val()))) {
    alert('Watts needs a username and password.')
    return false;
  }

  $('#loading_message').slideDown('fast');

  var username = $('[name=username]').val();
	var password = $('[name=password]').val();

	setCookie('tp_username', username, 9999);
	setCookie('tp_password', password, 9999);

	queryTrainingPeaks(username,password);
    
});

// removing the user, sending back to form
$('#clear_user').click(function() {
  clearUser();
});

function clearUser(){
  deleteCookie('tp_username');
  deleteCookie('tp_password');
  
  // clearing the contents of the workouts div
  $('#workouts h1').html('');
  $('#workouts h2').html('');
  $('#workouts p').html('');

  console.log("Removing association with user");
  
  toggleBackground(true);
  
  // clearing login form values
  $('[name=username]').val('');
  $('[name=password]').val('');
  
  showForm();
}

}); // end of document ready

</script>

</head>
<body>
  <div id="loading_message">
    Grabbing your workout...
  </div>
  
  <div id="tp_form">
    <form id="form" onsubmit="return false;">
      <fieldset>
        <label><input type="text" name="username" placeholder="TrainingPeaks Username" /></label>
        <label><input type="password" name="password" placeholder="Password" /></label>
      </fieldset>
      <fieldset>
        <input type="submit" id="tp_submit" value="Get Today's Workout">
      </fieldset>
    </form>
  </div>
  
  <div id="workouts">
    <div class="result_item" id="workoutresult">
      <h1></h1>
      <h2></h2>
      <p class="warmup"></p>
      <p class="workout"></p>
      <p class="cooldown"></p>
    </div>
    <h4>Workout for <span></span><a id="clear_user" href="#">&times;</a></h4>
  </div>
</body>
</html>