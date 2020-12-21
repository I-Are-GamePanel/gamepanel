$('document').ready(function(){
	editor = document.getElementById('editor');
	if(editor)
	{
		var quill = new Quill('#editor', {
			theme: 'snow'
		});
		$.getJSON("home.php?m=circular&get_circulars=true&type=cleared", function(data){
			if(data.length > 0)
			{
				$("head").append('<link rel="stylesheet" href="modules/circular/admin_notification.css">');
				$("h2").append('<i>There are ' + data.length + ' circulars&nbsp;<a href="?m=circular&list=true" >Manage Circulars</a></i>');
			}
		}).fail(function(){
			console.log('Failed reading JSON for circulars');
		});
	}
});

function remove_circulars()
{
	checkboxes = document.getElementsByClassName('circular_checkbox');
	var ids = [];
	for(var i = 0; i < checkboxes.length; i++)
	{ 
		if(checkboxes[i].checked)
		{
			ids.push(checkboxes[i].value);
		}
	}
	
	if(ids.length > 0)
	{
		var addpost = {};
		addpost["remove_circulars"] = "true";
		addpost["circulars_ids"] = ids;
		var destURL = "home.php?m=circular&list=true";
		var destURLCleared = destURL + "&type=cleared";
		$.ajax({
			type: "POST",
			url: destURLCleared,
			data: addpost,
			success: function(data){
				location.href = destURL;
			}
		});
	}
	
}

function swap_all_checkboxes(obj)
{ 
	checkboxes = document.getElementsByClassName('circular_checkbox');
	for(var i = 0; i < checkboxes.length; i++)
	{ 
		checkboxes[i].checked = obj.checked; 
	}
}

function toggle_all(obj, selectBox)
{ 
    if(typeof selectBox == "string")
	{ 
        selectBox = document.getElementById(selectBox);
		if(selectBox.type == "select-multiple")
		{ 
			for(var i = 0; i < selectBox.options.length; i++)
			{ 
				 selectBox.options[i].selected = obj.checked; 
			} 
		}
    }
}

function send_circular()
{
	var addpost = {};
	addpost['admins'] = $('#select_admins').val(),
	addpost['users'] = $('#select_users').val(),
	addpost['groups'] = $('#select_groups').val(),
	addpost['subusers_of_users'] = $('#select_subusers_of_users').val(),
	addpost['subject'] = $('#subject').val().trim(),
	addpost['message'] = document.getElementById("editor").getElementsByClassName("ql-editor")[0].innerHTML,
	addpost['send_circular'] = "send";
	
	if(addpost['admins'] == null && addpost['users'] == null && addpost['groups'] == null && addpost['subusers_of_users'] == null)
	{
		alert('Select at least one recipient (Send To).');
		return;
	}
	
	if(addpost['subject'] == null || addpost['subject'] == "")
	{
		alert('Introduce a subject.');
		return;
	}
	
	if(addpost['message'] == "<p><br></p>")
	{
		alert('Introduce a Message.');
		return;
	}
	
	var destURL = "home.php?m=circular";
	var destURLCleared = destURL + "&type=cleared";
	
	$.ajax({
		type: "POST",
		url: destURLCleared,
		data: addpost,
		success: function(data){
			alert(data);
			location.href = destURL;
		}
	});
}



