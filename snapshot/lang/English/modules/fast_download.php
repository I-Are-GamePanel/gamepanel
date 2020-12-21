<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2018 The OGP Development Team
 *
 * http://www.opengamepanel.org/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

define('OGP_LANG_create_alias', "Create alias and folder");
define('OGP_LANG_save_as', "Save As");
define('OGP_LANG_failure', "Error, failed to generate the alias file");
define('OGP_LANG_success', "Success");
define('OGP_LANG_fast_download_service_for', "Downloads redirection service for %s");
define('OGP_LANG_to_the_path', "To the path");
define('OGP_LANG_at_url', "at URL");
define('OGP_LANG_create_alias_for', "Create alias for");
define('OGP_LANG_fast_dl', "Redirecting downloads (FastDL)");
define('OGP_LANG_current_aliases_at_remote_server', "Current aliases at remote server");
define('OGP_LANG_delete_selected_aliases', "Delete selected aliases");
define('OGP_LANG_no_aliases_defined', "There are no web aliases defined by OGP for this remote server yet.");
define('OGP_LANG_fastdl_port', "Port");
define('OGP_LANG_fastdl_port_info', "Port on which your Fast Download Daemon will start.");
define('OGP_LANG_fastdl_ip', "Address");
define('OGP_LANG_fastdl_ip_info', "The IP address or domain in which your Fast Download server will start, the domain must to be listed at /etc/hosts.");
define('OGP_LANG_listing', "Listing");
define('OGP_LANG_listing_info', "If 'on', the server will list the contents of folders.");
define('OGP_LANG_fast_dl_advanced', "Advanced Settings");
define('OGP_LANG_apply_settings_and_restart_fastdl', "Save the daemon configuration and restart it");
define('OGP_LANG_stop_fastdl', "Stop Fast Download Daemon");
define('OGP_LANG_fast_download_daemon_running', "Fast Download Daemon is running.");
define('OGP_LANG_fast_download_daemon_not_running', "Fast Download Daemon is not running.");
define('OGP_LANG_fastdl_could_not_be_restarted', "The Fast Download service could not be restarted.");
define('OGP_LANG_configuration_file_could_not_be_written', "The configuration file could not be written.");
define('OGP_LANG_remove_folders', "Remove folder for selected aliases.");
define('OGP_LANG_remove_folder', "Remove folder");
define('OGP_LANG_delete_alias', "Delete alias");
define('OGP_LANG_no_game_homes_assigned', "You don't have any servers assigned to your account.");
define('OGP_LANG_select_remote_server', "Select remote server");
define('OGP_LANG_access_rules', "Access Rules");
define('OGP_LANG_create_aliases', "Create Aliases");
define('OGP_LANG_select_game', "Select game");
define('OGP_LANG_games_without_specified_rules', "Games without specified rules");
define('OGP_LANG_match_file_extension', "Match file extension");
define('OGP_LANG_match_file_extension_info', "Specify extensions separated by coma,<br> the matching files will be accessible.<br><b>Blank for unrestricted access</b>.");
define('OGP_LANG_match_client_ip', "Match client IP");
define('OGP_LANG_match_client_ip_info', "Connections with matching IP will be granted,<br>blank for unrestricted access. You can use<br>multiple IPs or ranges separated by coma:<br>/xx subnets<br>Example: 10.0.0.0/16<br>/xxx.xxx.xxx.xxx subnets<br>Example: 10.0.0.0/255.0.0.0<br>Hyphen ranges<br>Example: 10.0.0.5-230<br>Asterisk matching<br>Example: 10.0.*.*");
define('OGP_LANG_save_access_rules', "Save Access Rules");
define('OGP_LANG_create_access_rules', "Create access rules");
define('OGP_LANG_invalid_entries_found', "Invalid entries found");
define('OGP_LANG_game_name', "Game name");
define('OGP_LANG_alias_already_exists', "Alias %s already exists.");
define('OGP_LANG_warning_access_rules_applied_once_alias_created', "WARNING: Access rules are applied when the alias is created. No changes will be applied to the current aliases.");
define('OGP_LANG_autostart_on_agent_startup', "Autostart on Agent startup");
define('OGP_LANG_autostart_on_agent_startup_info', "Start the fast download daemon automatically when the Agent starts.");
define('OGP_LANG_port_forwarded_to_80', "Port forwarded from 80");
define('OGP_LANG_port_forwarded_to_80_info', "Enable this option if the port configured for this fast download daemon has been forwarded from port 80, so the port will be hidden at URLs.");
define('OGP_LANG_current_access_rules', "Current access rules");
?>