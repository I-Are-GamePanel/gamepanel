$('document').ready(function(){
	$.getJSON("home.php?m=circular&p=show_circular&type=cleared", function(data){
		if(data.length > 0)
		{
			$("head").append('<link rel="stylesheet" href="modules/circular/notification.css">');
			$("body").append('<div id="notification">You have ' + data.length + ' pending circulars.<br><a href="?m=circular&p=show_circular&list=true" >Read Circulars</a></div>');
		}
	}).fail(function(){
		console.log('Failed reading JSON for circulars');
	});
});