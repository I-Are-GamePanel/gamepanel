$(function() {
	$("#phpIniButton").click(function(e){
		var units = ['k', 'm', 'g', 't', 'p'];

		var maxSize = $("#attachment_max_size").val();
		var limit = parseInt($("#attachment_limit").val());

		var unit = maxSize.slice(-2, -1);
		var sizeNoUnit = maxSize.slice(0, -2);

		var post_max_size = "";
		if ($.inArray(unit.toLowerCase(), units) != -1) {
			post_max_size = (sizeNoUnit * (limit+1)) + unit;
		}

		var str = "<pre>";
		str += "file_uploads = On\n";
		str += "upload_max_filesize = " + sizeNoUnit + unit + "\n";
		str += "max_file_uploads = " + limit + "\n";
		
		if (post_max_size.length !== 0) {
			str += "post_max_size = " + post_max_size + "\n";
		}

		str += "</pre>";

		$("#guesstimateIniSettings").css("display", "block");
		$("#guesstimateIniSettings").html(str);

	});
});