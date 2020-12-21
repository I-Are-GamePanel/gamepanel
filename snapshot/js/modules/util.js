$(function(){
	$("#tabs").tabs();
	$("#loading").removeClass('hide').addClass('show');
	var select_agent = $('#translation').attr('data-select_agent'),
		target_empty = $('#translation').attr('data-target_empty'),
		command_empty = $('#translation').attr('data-command_empty'),
		agent_invalid = $('#translation').attr('data-agent_invalid'),
		post_failed = $('#translation').attr('data-post_failed'),
		select_server = $('#translation').attr('data-select_server'),
		select_server_option = $('#translation').attr('data-select_server_option');
	
	// Load the agents via an external script so the user isn't waiting an eternity if they have several agents and multiple are offline
	$.getJSON("home.php?m=util&p=agents&type=cleared", function(data){
		var agents = "";
		var agentsOnline = 0;
		
		$("#loading_agents").removeClass('show').addClass('hide');
		$("#loading").removeClass('show').addClass('hide');
		$("#options").removeClass('hide').addClass('show');
		
		agents += "<label for=\"agent\">" + select_agent + "</label>\r\n"
		agents += "<select id=\"agent\" name=\"agent\">\r\n"
		
		for(var i = 0; i<data.length; ++i){
			agents += "\t<option value=\"" + data[i]['id'] + "\"" + (data[i]['status']===0?" disabled":"") + ">" + data[i]['name'] +"</option>\r\n";
			
			if(data[i]['status'] == 1){
				++agentsOnline;
			}
		}
		
		agents += "</select>";
		
		if(agentsOnline == 0){
			$("#options").removeClass('show').addClass('hide');
			$("#agents_offline").removeClass('hide').addClass('show');
			
			// Hide the sourcemod add admin form if all agents are offline.
			$("#addadmin_form").addClass('hide');
			$("#all_servers_offline").removeClass('hide').addClass('show');
		}else if($("#command option").length == 0){
			$("#options").removeClass('show').addClass('hide');
			$("#no_commands").removeClass('hide').addClass('show');
		}else{
			$("#select_agent").html(agents);
		}
	}).fail(function(){
		$("#loading_agents").removeClass('show').addClass('hide');
		$("#loading").removeClass('show').addClass('hide');
		$("#loading_failed").removeClass('hide').addClass('show');
	});
	
	// Handle the network_tools form.
	$("#network_tools").on("submit", function(e){
		var target = $("#remote_target").val();
		var agent = $("#agent").val();
		var command = $("#command").val();
		
		// Some validation browser-side. Still need to do the same server-side.
		if(target.length === 0){
			// target input is empty.
			$("#output").removeClass('hide').addClass('show').html("<pre>" + target_empty + "</pre>");
		}else if(agent.length === 0){
			// We'll only get to this point if there's no agents, or all agents are offline but the form was still submitted.
			$("#output").removeClass('hide').addClass('show').html("<pre>" + agent_invalid + "</pre>");
		}else if(command.length === 0){
			// We'll only get to this point if there's no command specified.
			$("#output").removeClass('hide').addClass('show').html("<pre>" + command_empty + "</pre>");
		}else{
			$("#loading").removeClass('hide').addClass('show');
			$("#output").removeClass('show').addClass('hide');
			
			$("#network_tools button").prop({disabled:true}); // Disable the submit button if we're about to post - preventing stuff being ran several times via spamming the button and/or enter.
			$.post("home.php?m=util&p=network_tools&type=cleared", $("#network_tools").serialize(), function(postCommand){
				$("#loading").removeClass('show').addClass('hide');
				$("#output").removeClass('hide').addClass('show').html("<pre>" + postCommand + "</pre>");
				
				$("#network_tools button").prop({disabled:false});
			}).fail(function(){
				$("#loading").removeClass('show').addClass('hide');
				$("#output").removeClass('hide').addClass('show').html("<pre>" + post_failed + "</pre>");
				
				$("#network_tools button").prop({disabled:false});
			});
		}
		e.preventDefault();
	});// ./end network_tools form handling.
	
	// ----- Sourcemod Admins -----
	$.getJSON("home.php?m=util&p=addadmin_helper&type=cleared", function(gs){
		if(gs.length === 0){
			$("#no_servers").removeClass('hide').addClass('show');
		}else{
			var games = "";
			$("#add_admin").removeClass('hide').addClass('show');
			
			games += "<label for=\"gameserver_id\">" + select_server + "</label>\r\n"
			games += "<select id=\"gameserver_id\" name=\"gameserver_id\">\r\n"
			games += "<option value=\"0\" selected>" + select_server_option + "</option>\r\n"
			
			for(var i = 0; i<gs.length; ++i){
				games += "\t<option value=\"" + gs[i]['home_id'] + "\">" + gs[i]['home_name'] +"</option>\r\n";
			}
			
			games += "</select>";
			$("#games").html(games);
			
			$("#gameserver_id").change(function(){
				var home = $("#gameserver_id").val();
				var gameserver_id = $("#gameserver_id").val();
				
				// although the disabled attribute is put on the option, set all the values to 0 if it's somehow chosen again via the form being edited
				if(gameserver_id == 0){
					$("#remote_server_id").val(0);
					$("#gameserver_name").val(0);
					$("#gameserver_ip").val(0);
					$("#gameserver_port").val(0);
				}else{
					for(var i = 0; i<gs.length; ++i){
						if(home === gs[i]['home_id']){
							$("option[value='0']").attr("disabled", "disabled");
							$("#remote_server_id").val(gs[i]['remote_server_id']);
							$("#gameserver_name").val(gs[i]['game_name']);
							$("#gameserver_ip").val(gs[i]['ip']);
							$("#gameserver_port").val(gs[i]['port']);
							break;
						}
					}
				}
			});
		}
	}); // ./end Sourcemod Admins
		
	$("#sourcemod_perms").change(function(){
		var sourcemod_perms = $("#sourcemod_perms").val();
		
		if(sourcemod_perms === 'root'){
			$("#sourcemod_flagList").removeClass('show').addClass('hide');
			$('.item input[type="checkbox"]').prop('checked', false);
		}else{
			$("#sourcemod_flagList").removeClass('hide').addClass('show');
		}
	});
	
	// Process the sourcemod admin form on submission.
	$("#addadmin_form").on("submit", function(e){
		$("#addadmin_form button").prop({disabled:true});
		
		var errors = 0;
		var remoteId = $("#remote_server_id").val();
		var gameName = $("#gameserver_name").val();
		var gameIp = $("#gameserver_ip").val();
		var gamePort = $("#gameserver_port").val();
		var addSteamid = $("#addSteamid").val();
		var immunity = $("#immunity").val();
		
		// Set the message divs back to defaults.
		$("#invalid_server").removeClass('show').addClass('hide');
		$("#invalid_steamid_admin").removeClass('show').addClass('hide');
		$("#invalid_response_admin").removeClass('show').addClass('hide');
		$("#invalid_immunity").removeClass('show').addClass('hide');
		$("#addadmin_response").removeClass('show').addClass('hide');
		
		if(remoteId.length === 0 || gameName.length === 0 || gameIp.length === 0 || gamePort.length === 0){
			$("#invalid_server").removeClass('hide').addClass('show');
			++errors;
		}
		
		if(!(addSteamid.match(/^STEAM_[01]:[01]:\d+$/))){
			$("#invalid_steamid_admin").removeClass('hide').addClass('show');
			++errors;
		}
		
		if(immunity.length > 2 || isNaN(immunity)){
			$("#invalid_immunity").removeClass('hide').addClass('show');
			++errors;
		}
		
		if(errors === 0){
			$.post("home.php?m=util&p=addadmin_helper&type=cleared", $("#addadmin_form").serialize(), function(postCommand){
				$("#addadmin_response").removeClass('hide').addClass('show').html(postCommand);
				$("#addadmin_form button").prop({disabled:false});
			}).fail(function(){
				$("#invalid_response_admin").removeClass('hide').addClass('show');
				$("#addadmin_form button").prop({disabled:false});
			});
		}else{
			$("#addadmin_form button").prop({disabled:false});
		}
		
		e.preventDefault();
	}); // add_admin form handling
	
	// ----- AMX mod Admins -----
	$.getJSON("home.php?m=util&p=amx_addadmin_helper&type=cleared", function(gs){
		if(gs.length === 0){
			$("#amx_no_servers").removeClass('hide').addClass('show');
		}else{
			var games = "";
			$("#amx_add_admin").removeClass('hide').addClass('show');
			
			games += "<label for=\"amx_gameserver_id\">" + select_server + "</label>\r\n"
			games += "<select id=\"amx_gameserver_id\" name=\"amx_gameserver_id\">\r\n"
			games += "<option value=\"0\" selected>" + select_server_option + "</option>\r\n"
			
			for(var i = 0; i<gs.length; ++i){
				games += "\t<option value=\"" + gs[i]['home_id'] + "\">" + gs[i]['home_name'] +"</option>\r\n";
			}
			
			games += "</select>";
			$("#amx_games").html(games);
			
			$("#amx_gameserver_id").change(function(){
				var home = $("#amx_gameserver_id").val();
				var gameserver_id = $("#amx_gameserver_id").val();
				
				// although the disabled attribute is put on the option, set all the values to 0 if it's somehow chosen again via the form being edited
				if(gameserver_id == 0){
					$("#amx_remote_server_id").val(0);
					$("#amx_gameserver_name").val(0);
					$("#amx_gameserver_ip").val(0);
					$("#amx_gameserver_port").val(0);
				}else{
					for(var i = 0; i<gs.length; ++i){
						if(home === gs[i]['home_id']){
							$("option[value='0']").attr("disabled", "disabled");
							$("#amx_remote_server_id").val(gs[i]['remote_server_id']);
							$("#amx_gameserver_name").val(gs[i]['game_name']);
							$("#amx_gameserver_ip").val(gs[i]['ip']);
							$("#amx_gameserver_port").val(gs[i]['port']);
							break;
						}
					}
				}
			});
		}
	}); // ./end AMX mod Admins
	
	$("#amx_mod_perms").change(function(){
		var amx_mod_perms = $("#amx_mod_perms").val();
		
		if(amx_mod_perms === 'root'){
			$("#amx_mod_flagList").removeClass('show').addClass('hide');
			$('.amx_item input[type="checkbox"]').prop('checked', false);
		}else{
			$("#amx_mod_flagList").removeClass('hide').addClass('show');
		}
	});
	
	$("#amx_login_type").change(function(){
		var amx_login_type = $("#amx_login_type").val();
		
		if(amx_login_type === 'amx_login_steamid'){
			$("#amx_login_nick_pass").removeClass('show').addClass('hide');
			$("#amx_Nickname").prop('required',false);
			$("#amx_Password").prop('required',false);
			$("#amx_Steamid").prop('required',true);
			$("#amx_login_steamid").removeClass('hide').addClass('show');
		}else{
			$("#amx_login_steamid").removeClass('show').addClass('hide');
			$("#amx_Nickname").prop('required',true);
			$("#amx_Password").prop('required',true);
			$("#amx_Steamid").prop('required',false);
			$("#amx_login_nick_pass").removeClass('hide').addClass('show');
		}
	});
	
	// Process the amx mod admin form on submission.
	$("#amx_addadmin_form").on("submit", function(e){
		$("#amx_addadmin_form button").prop({disabled:true});
		
		var errors = 0;
		var remoteId = $("#amx_remote_server_id").val();
		var gameName = $("#amx_gameserver_name").val();
		var gameIp = $("#amx_gameserver_ip").val();
		var gamePort = $("#amx_gameserver_port").val();
		var login_type = $("#amx_login_type").val();
		
		// Set the message divs back to defaults.
		$("#amx_invalid_server").removeClass('show').addClass('hide');
		$("#amx_invalid_nickname_admin").removeClass('show').addClass('hide');
		$("#amx_invalid_password_admin").removeClass('show').addClass('hide');
		$("#amx_invalid_steamid_admin").removeClass('show').addClass('hide');
		$("#amx_invalid_response_admin").removeClass('show').addClass('hide');
		$("#amx_addadmin_response").removeClass('show').addClass('hide');
		
		if(login_type == 'amx_login_steamid')
		{
			var Steamid = $("#amx_Steamid").val();
			if(!(Steamid.match(/^STEAM_[01]:[01]:\d+$/))){
				$("#amx_invalid_steamid_admin").removeClass('hide').addClass('show');
				++errors;
			}
		}
		else
		{
			var Nickname = $("#amx_Nickname").val();
			var Password = $("#amx_Password").val();
			if(!(Nickname.match(/^[^\s][A-zÀ-ÿ0-9 !@)(,}/|\.:?;{#$%&*+=-]{1,28}[^\s]$/))){
				$("#amx_invalid_nickname_admin").removeClass('hide').addClass('show');
				++errors;
			}
			if(!(Password.match(/^[^\s][A-zÀ-ÿ0-9 !@)(,}/|\.:?;{#$%&*+=-]{1,28}[^\s]$/))){
				$("#amx_invalid_password_admin").removeClass('hide').addClass('show');
				++errors;
			}
		}
		
		if(remoteId.length === 0 || gameName.length === 0 || gameIp.length === 0 || gamePort.length === 0){
			$("#amx_invalid_server").removeClass('hide').addClass('show');
			++errors;
		}
		
		if(errors === 0){
			$.post("home.php?m=util&p=amx_addadmin_helper&type=cleared", $("#amx_addadmin_form").serialize(), function(postCommand){
				$("#amx_addadmin_response").removeClass('hide').addClass('show').html(postCommand);
				$("#amx_addadmin_form button").prop({disabled:false});
			}).fail(function(){
				$("#amx_invalid_response_admin").removeClass('hide').addClass('show');
				$("#amx_addadmin_form button").prop({disabled:false});
			});
		}else{
			$("#amx_addadmin_form button").prop({disabled:false});
		}
		e.preventDefault();
	}); // add_admin form handling
	
	// ----- Steam Converter -----
	$("#steam_converter").on("submit", function(e){
		$("#steam_converter button").prop({disabled:true});
		$.post("home.php?m=util&p=steamid_converter&type=cleared", $("#steam_converter").serialize(), function(steam_data){
			var json = $.parseJSON(steam_data);
			
			if(json.length === 0){
				$("#steam_info").removeClass('show').addClass('hide');
				$("#invalid_steamid").removeClass('hide').addClass('show');
				$("#invalid_response").removeClass('show').addClass('hide');
				
				$("#steam_converter button").prop({disabled:false});
			}else{
				$("#steam_info").removeClass('hide').addClass('show');
				$("#invalid_steamid").removeClass('show').addClass('hide');
				$("#invalid_response").removeClass('show').addClass('hide');
				
				$("#steamLink").html('<b>Profile Link:</b> ' + json.steamProfile);
				$("#steamId").html('<b>Legacy ID:</b> ' + json.steamId);
				$("#steamId3").html('<b>SteamID3:</b> ' + json.steamId3);
				$("#steamId64").html('<b>SteamID64:</b> ' + json.communityId);
				
				$("#steam_converter button").prop({disabled:false});
			}
		}).fail(function(){
			$("#invalid_steamid").removeClass('show').addClass('hide');
			$("#steam_info").removeClass('show').addClass('hide');
			$("#invalid_response").removeClass('hide').addClass('show');
		});
		e.preventDefault();
	}); // ./end steam_converter form handling.
	
	$("#your-address").click(function(){
		$("#remote_target").val($("#your-address").text());
	});
});