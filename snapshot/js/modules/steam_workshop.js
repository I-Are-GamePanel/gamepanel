function GetURLParameter(sParam)
{
	var sPageURL = window.location.search.substring(1);
	var sURLVariables = sPageURL.split('&');
	for (var i = 0; i < sURLVariables.length; i++)
	{
		var sParameterName = sURLVariables[i].split('=');
		if (sParameterName[0] == sParam)
		{
			return sParameterName[1];
		}
	}
}

function isValidForm(currentForm)
{
	currentForm
	var inputs = currentForm.getElementsByTagName("input");
	for(var i = 0; i < inputs.length; i++) {
		if(inputs[i].type == "checkbox") {
			if(inputs[i].checked)
			{
				return true;
			}
		}
		if(inputs[i].type == "text") {
			if(inputs[i].value.match(/^([0-9]+,?)+$/g))
			{
				return true;
			}
		}
	}
	window.alert(currentForm.getAttribute('data-form-error'));
	return false;
}

$(document).ready(function(){
	if( document.getElementById("dialog") && GetURLParameter('get_sgc') == 'show' )
	{
		var hmip = GetURLParameter('home_id-mod_id-ip-port');
		var returnUrl = "home.php?m=steam_workshop&p=main&home_id-mod_id-ip-port="+hmip+"&show_log"
		var addpost = {};
		addpost.sgc = '';
		$('#dialog').html("<label for='SteamGuardCode'>Steam Guard:</label>\n"+
						  "<input class='SteamGuardCode' type=text style='width:99%;' name='sgc'>");
		$('#dialog').dialog({
			autoOpen: true,
			width: 450,
			modal: true,
			buttons: [{ text: "Send Code", click: function(){
					addpost.sgc = $('input[class="SteamGuardCode"]').val();
					$.ajax({
						type: "POST",
						url: returnUrl,
						data: addpost,
						async: false,
						success: function(data){
							$('#dialog').html("<div class=\"loader\"></div>");
							setTimeout(function(){
								window.location.href = returnUrl;
							}, 10000);
						}
					});
				}
			}],
			close: function() {
				$( this ).dialog( "close" );
			}
		});
	}
	bbcode_containers = document.getElementsByClassName("bbcode_container")
	if(bbcode_containers.length > 0)
	{
		for(var i = 0; i < bbcode_containers.length; i++) {
			var result = XBBCODE.process({
				text: bbcode_containers[i].innerText,
				removeMisalignedTags: true,
				addInLineBreaks: true
			});
			bbcode_containers[i].innerHTML = result.html;
		}
	}
});
