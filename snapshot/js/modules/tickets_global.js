$(function() {
	$.getJSON("home.php?m=tickets&p=notifications&type=cleared", function(data) {
		if (data['notificationCount'] > 0) {
			var menutickets = document.getElementsByClassName('tickets');
			menutickets[0].innerHTML += "<text style='background:#aa0000;color:white;border-radius:20px;border:1px solid white;margin-left:5px'>&nbsp;&nbsp;<b>" + data['notificationCount'] + "</b>&nbsp;&nbsp;</text>";
		}
	});
});
