<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2017 The OGP Development Team
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

$module_title = "Tickets";
$module_version = "1.0a";
$db_version = 3;
$module_required = false;
$module_menus = array(
					array(
						'name'		=>	'Support Tickets',
						'group'		=>	'user',
					),

					array(
						'name'		=>	'Support Ticket Settings',
						'group'		=>	'admin',
						'subpage'	=>	'ticket_settings',
					),
				);

$install_queries[0] = array(
	"DROP TABLE IF EXISTS `".OGP_DB_PREFIX."ticket_replies`",

	"DROP TABLE IF EXISTS `".OGP_DB_PREFIX."ticket_messages`",
	"DROP TABLE IF EXISTS `".OGP_DB_PREFIX."ticket_attachments`",
	"DROP TABLE IF EXISTS `".OGP_DB_PREFIX."ticket_settings`",
	"DROP TABLE IF EXISTS `".OGP_DB_PREFIX."tickets`",

	"CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."tickets` (
		`tid` int NOT NULL AUTO_INCREMENT,
		`uid` varchar(32) NOT NULL UNIQUE,
		`user_id` int NOT NULL,
		`parent_id` int NOT NULL,
		`user_ip` varbinary(16) NOT NULL,
		`subject` varchar(64) NOT NULL,
		`service_id` int,
		`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		`last_updated` varchar(22),
		`status` tinyint NOT NULL,
		`assigned_to` tinyint,
		PRIMARY KEY (`tid`)
	);",

	"CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."ticket_messages` (
		`reply_id` int NOT NULL AUTO_INCREMENT,
		`ticket_id` int NOT NULL,
		`user_id` int NOT NULL,
		`user_ip` varbinary(16) NOT NULL,
		`message` TEXT NOT NULL,
		`date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		`rating` tinyint DEFAULT '0',
		`is_admin` int DEFAULT '0',
		PRIMARY KEY (`reply_id`)
	);",

	"ALTER TABLE `".OGP_DB_PREFIX."ticket_messages` ADD CONSTRAINT `".OGP_DB_PREFIX."ticket_messages_fk0` FOREIGN KEY (`ticket_id`) REFERENCES `".OGP_DB_PREFIX."tickets`(`tid`);",
);

$install_queries[1] = array(
	"CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."ticket_attachments` (
		`attachment_id` int NOT NULL AUTO_INCREMENT,
		`ticket_id` int NOT NULL,
		`reply_id` int,
		`original_name` varchar(255) NOT NULL,
		`unique_name` varchar(32) NOT NULL UNIQUE,
		PRIMARY KEY (`attachment_id`)
	);",
);

$install_queries[2] = array(
	"CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."ticket_settings` (
		`id` INT NOT NULL AUTO_INCREMENT,
		`setting_name` varchar(32) NOT NULL UNIQUE,
		`setting_value` TEXT NOT NULL,
		PRIMARY KEY (`id`)
	);",
	
	"INSERT INTO `".OGP_DB_PREFIX."ticket_settings` (setting_name, setting_value) VALUES ('ratings_enabled', true) ON DUPLICATE KEY UPDATE `setting_name` = 'ratings_enabled', `setting_value` = true",
	"INSERT INTO `".OGP_DB_PREFIX."ticket_settings` (setting_name, setting_value) VALUES ('attachments_enabled', true) ON DUPLICATE KEY UPDATE `setting_name` = 'attachments_enabled', `setting_value` = true",
	"INSERT INTO `".OGP_DB_PREFIX."ticket_settings` (setting_name, setting_value) VALUES ('attachment_max_size', '52428800') ON DUPLICATE KEY UPDATE `setting_name` = 'attachment_max_size', `setting_value` = '52428800'",
	"INSERT INTO `".OGP_DB_PREFIX."ticket_settings` (setting_name, setting_value) VALUES ('attachment_limit', '5') ON DUPLICATE KEY UPDATE `setting_name` = 'attachment_limit', `setting_value` = '5'",
	"INSERT INTO `".OGP_DB_PREFIX."ticket_settings` (setting_name, setting_value) VALUES ('attachment_save_dir', '".__DIR__ . '/uploads' ."') ON DUPLICATE KEY UPDATE `setting_name` = 'attachment_save_dir', `setting_value` = '".__DIR__ . '/uploads' ."'",
	"INSERT INTO `".OGP_DB_PREFIX."ticket_settings` (setting_name, setting_value) VALUES ('attachment_extensions', 'jpg, gif, jpeg, jpg, png, pdf, txt, sql, zip') ON DUPLICATE KEY UPDATE `setting_name` = 'attachment_extensions', `setting_value` = 'jpg, gif, jpeg, jpg, png, pdf, txt, sql, zip'",
);

$install_queries[3] = array(
	"INSERT INTO `".OGP_DB_PREFIX."ticket_settings` (setting_name, setting_value) VALUES ('notifications_enabled', true) ON DUPLICATE KEY UPDATE `setting_name` = 'notifications_enabled', `setting_value` = true",
);
