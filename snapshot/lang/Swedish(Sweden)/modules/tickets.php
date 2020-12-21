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

define('OGP_LANG_support_tickets', "Support Tickets");
define('OGP_LANG_ticket_subject', "Subject");
define('OGP_LANG_ticket_status', "Status");
define('OGP_LANG_ticket_updated', "Last Updated");
define('OGP_LANG_ticket_options', "Options");
define('OGP_LANG_viewing_ticket', "Viewing Ticket");
define('OGP_LANG_ticket_not_found', "The given ticket parameters don't match an existing ticket.");
define('OGP_LANG_ticket_cant_read', "Insufficient permission to view ticket.");
define('OGP_LANG_cant_view_ticket', "Unable to retrieve the ticket information.");
define('OGP_LANG_ticket_id', "Ticket ID");
define('OGP_LANG_service_id', "Service ID");
define('OGP_LANG_ticket_submitted', "Ticket Submitted");
define('OGP_LANG_submitter_info', "Submitter Info");
define('OGP_LANG_name', "Name");
define('OGP_LANG_ip', "IP");
define('OGP_LANG_role', "User Role");
define('OGP_LANG_ticket_submit_response', "Submit Response");
define('OGP_LANG_ticket_close', "Close");
define('OGP_LANG_no_ticket_replies', "No Ticket Replies");
define('OGP_LANG_no_tickets_submitted', "No Tickets have been submitted.");
define('OGP_LANG_submit_ticket', "Submit Ticket");
define('OGP_LANG_ticket_service', "Service");
define('OGP_LANG_ticket_message', "Message");
define('OGP_LANG_ticket_errors_occured', "The following errors occured when submitting your ticket");
define('OGP_LANG_no_ticket_subject', "No Ticket Subject");
define('OGP_LANG_invalid_ticket_subject_length', "Invalid Subject Length (4 to 64 characters)");
define('OGP_LANG_invalid_home_selected', "Invalid Home Selected");
define('OGP_LANG_no_ticket_message', "No Ticket Message Provided");
define('OGP_LANG_invalid_ticket_message_length', "Invalid Ticket Message Length (Minimum of 4 characters)");
define('OGP_LANG_ticket_no_service', "No service selected for this ticket.");
define('OGP_LANG_failed_to_open', "Failed to open the ticket.");
define('OGP_LANG_failed_to_reply', "Failed to create response to ticket.");
define('OGP_LANG_no_ticket_reply', "No Ticket Reply Provided");
define('OGP_LANG_invalid_ticket_reply_length', "Invalid Ticket Reply Length (Minimum of 4 characters)");
define('OGP_LANG_ticket_closed', "Ticket Closed");
define('OGP_LANG_ticket_open', "Ticket Open");
define('OGP_LANG_ticket_admin_response', "Admin Response");
define('OGP_LANG_ticket_customer_response', "Customer Response");
define('OGP_LANG_ticket_invalid_page_num', "You have attempted to view a page number with no tickets!");
define('OGP_LANG_ticket_is_closed', "This ticket is closed. You may reply to this ticket to reopen it.");
define('OGP_LANG_reply', "Reply");
define('OGP_LANG_invalid_rating', "The rating received is not valid.");
define('OGP_LANG_successfully_rated_response', "Successfully rated response.");
define('OGP_LANG_failed_rating_response', "Failed to rate the response.");
define('OGP_LANG_attachment_not_all_parameters_sent', "Not all parameters have been sent to download the file.");
define('OGP_LANG_requested_attachment_missing', "The requested attachment does not exist.");
define('OGP_LANG_requested_attachment_missing_db', "The requested attachment does not exist in the database.");
define('OGP_LANG_ratings_disabled', "Rating responses is not enabled.");
define('OGP_LANG_attachments', "Attachments");
define('OGP_LANG_add_file_attachment', "Add More");
define('OGP_LANG_attachment_size_info', "Each selected file may be a maximum of %s");
define('OGP_LANG_attachment_file_size_info', "A maximum of %s file(s) may be uploaded, %s each.");
define('OGP_LANG_attachment_allowed_extensions_info', "Allowed File Extensions: %s");
define('OGP_LANG_ticket_fix_before_submitting', "Please fix the following errors before submitting the ticket");
define('OGP_LANG_ticket_fix_before_replying', "Please fix the following errors before replying to the ticket");
define('OGP_LANG_ticket_problem_with_attachments', "There was a problem with the file(s) you attached");
define('OGP_LANG_ticket_attachment_invalid_extension', "%1 does not contain a permitted extension.");
define('OGP_LANG_ticket_attachment_invalid_size', "%1 is larger than the allowed file size. %2 maximum!");
define('OGP_LANG_ticket_max_file_elements', "Only a maximum of %1 file inputs may exist.");
define('OGP_LANG_ticket_attachment_multiple_files', "One or more file inputs have multiple files selected.");
define('OGP_LANG_attachment_err_ini_size', "%s (%s) exceeds the 'upload_max_filesize' setting.");
define('OGP_LANG_attachment_err_partial', "%s was only partially uploaded.");
define('OGP_LANG_attachment_err_no_tmp', "No tmp directory exists to save %s");
define('OGP_LANG_attachment_err_cant_write', "Unable to write %s to disk.");
define('OGP_LANG_attachment_err_extension', "An extension stopped the upload of %s. Review your logs.");
define('OGP_LANG_attachment_too_large', "%s (%s) is larger than the maximum allowed size of %s!");
define('OGP_LANG_attachment_forbidden_type', "The file type of %s may not be uploaded.");
define('OGP_LANG_attachment_directory_not_writable', "Unable to save the attached files. The specified save directory is not writable.");
define('OGP_LANG_attachment_invalid_file_count', "The amount of files sent to the server was invalid. Only a maximum of %s may be uploaded");
define('OGP_LANG_ratings_enabled', "Ratings");
define('OGP_LANG_ratings_enabled_info', "Set if rating responses should be allowed.");
define('OGP_LANG_attachments_enabled', "Attachments");
define('OGP_LANG_attachments_enabled_info', "Set if the attachment system should be enabled.");
define('OGP_LANG_attachment_max_size', "Max File Size");
define('OGP_LANG_attachment_max_size_info', "Sets the max file size for attachments.");
define('OGP_LANG_attachment_limit', "Attachment Limit");
define('OGP_LANG_attachment_limit_info', "Sets how many files may be attached at once. 0 for no limit.");
define('OGP_LANG_attachment_save_dir', "Attachment Upload Location");
define('OGP_LANG_attachment_save_dir_info', "Sets where attachments should be uploaded. Ideally, outside of the public_html folder or direct access blocked.");
define('OGP_LANG_attachment_extensions', "Attachment Extensions");
define('OGP_LANG_attachment_extensions_info', "Sets the permitted extensions. Each extension should be separated by a comma.");
define('OGP_LANG_show_php_ini', "Show Estimated INI Settings");
define('OGP_LANG_settings_errors_occured', "The following errors occured when attempting to update the settings - not everything has been updated!");
define('OGP_LANG_invalid_max_size', "Invalid value for Max Size setting.");
define('OGP_LANG_invalid_unit', "Invalid unit type for Max Size setting. Expecting KB, MB, GB, TB, or PB.");
define('OGP_LANG_invalid_save_dir', "The specified save directory does not exist and can not be created.");
define('OGP_LANG_invalid_save_dir_not_writable', "The specified save directory exists but is not writable.");
define('OGP_LANG_invalid_extensions', "No attachment extensions have been specified.");
define('OGP_LANG_update_settings', "Update Settings");
define('OGP_LANG_notifications_enabled', "Notifications");
define('OGP_LANG_notifications_enabled_info', "Allow the user/admin to see if they have got a ticket awaiting reply.");
