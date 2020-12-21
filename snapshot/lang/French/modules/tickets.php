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

define('OGP_LANG_support_tickets', "Tickets de Support");
define('OGP_LANG_ticket_subject', "Sujet");
define('OGP_LANG_ticket_status', "Statut");
define('OGP_LANG_ticket_updated', "Dernière mise à jour");
define('OGP_LANG_ticket_options', "Options");
define('OGP_LANG_viewing_ticket', "Voir le Ticket");
define('OGP_LANG_ticket_not_found', "Les paramètres du Ticket ne correspondent à aucun Ticket existant.");
define('OGP_LANG_ticket_cant_read', "Permissions insuffisantes pour voir le ticket.");
define('OGP_LANG_cant_view_ticket', "Impossible d'obtenir les informations du ticket.");
define('OGP_LANG_ticket_id', "ID du Ticket");
define('OGP_LANG_service_id', "ID du Service");
define('OGP_LANG_ticket_submitted', "Ticket soumis le");
define('OGP_LANG_submitter_info', "Envoyé par");
define('OGP_LANG_name', "Nom");
define('OGP_LANG_ip', "IP");
define('OGP_LANG_role', "Rôle de l'utilisateur");
define('OGP_LANG_ticket_submit_response', "Envoyer la Réponse");
define('OGP_LANG_ticket_close', "Fermer le Ticket");
define('OGP_LANG_no_ticket_replies', "Pas de Réponse au Ticket");
define('OGP_LANG_no_tickets_submitted', "Aucun Ticket n'a été envoyé.");
define('OGP_LANG_submit_ticket', "Ouvrir un Ticket");
define('OGP_LANG_ticket_service', "Service");
define('OGP_LANG_ticket_message', "Message");
define('OGP_LANG_ticket_errors_occured', "L'erreur suivante s'est produite lors de l'envoi du Ticket");
define('OGP_LANG_no_ticket_subject', "Pas de Sujet pour le Ticket");
define('OGP_LANG_invalid_ticket_subject_length', "Longueur du Sujet invalide (4 à 64 caractères)");
define('OGP_LANG_invalid_home_selected', "Service sélectionné invalide");
define('OGP_LANG_no_ticket_message', "Pas de Message pour le Ticket");
define('OGP_LANG_invalid_ticket_message_length', "Longueur du Message invalide pour le Ticket (minimum de 4 caractères)");
define('OGP_LANG_ticket_no_service', "Pas de Service sélectionné pour le Ticket.");
define('OGP_LANG_failed_to_open', "Impossible d'ouvrir le Ticket.");
define('OGP_LANG_failed_to_reply', "Impossible de créer la Réponse au Ticket.");
define('OGP_LANG_no_ticket_reply', "Pas de Réponse fournie pour le Ticket");
define('OGP_LANG_invalid_ticket_reply_length', "Longueur de la Réponse invalide pour le Ticket (minimum de 4 caractères)");
define('OGP_LANG_ticket_closed', "Ticket Fermé");
define('OGP_LANG_ticket_open', "Ticket Ouvert");
define('OGP_LANG_ticket_admin_response', "Réponse Admin");
define('OGP_LANG_ticket_customer_response', "Réponse Client");
define('OGP_LANG_ticket_invalid_page_num', "Vous avez tenté d'accéder à un numéro de page sans Ticket!");
define('OGP_LANG_ticket_is_closed', "Le Ticket est fermé. Vous pouvez répondre au Ticket pour le rouvrir.");
define('OGP_LANG_reply', "Répondre");
define('OGP_LANG_invalid_rating', "La note reçue n'est pas valide.");
define('OGP_LANG_successfully_rated_response', "Réponse correctement notée.");
define('OGP_LANG_failed_rating_response', "Erreur lors de l'évaluation de la réponse.");
define('OGP_LANG_attachment_not_all_parameters_sent', "Tous les paramètres n'ont pas étés envoyés pour télécharger le fichier.");
define('OGP_LANG_requested_attachment_missing', "La pièce jointe demandée n'existe pas.");
define('OGP_LANG_requested_attachment_missing_db', "La pièce jointe demandée n'existe pas dans la base de données.");
define('OGP_LANG_ratings_disabled', "Noter les réponses n'est pas activé.");
define('OGP_LANG_attachments', "Pièces Jointes");
define('OGP_LANG_add_file_attachment', "Ajouter Plus");
define('OGP_LANG_attachment_size_info', "Chaque fichier sélectionné ne doit pas dépasser %s");
define('OGP_LANG_attachment_file_size_info', "Un maximum de %s fichier(s) peut être envoyé, %s chacun.");
define('OGP_LANG_attachment_allowed_extensions_info', "Extensions de Fichier Autorisées: %s");
define('OGP_LANG_ticket_fix_before_submitting', "Veuillez corriger l'erreur suivante avant de soumettre le ticket");
define('OGP_LANG_ticket_fix_before_replying', "Veuillez corriger l'erreur suivante avant de répondre au ticket");
define('OGP_LANG_ticket_problem_with_attachments', "Une erreur est survenue avec le(s) fichier(s) que vous avez joint.");
define('OGP_LANG_ticket_attachment_invalid_extension', "%1 ne contient pas d'extension autorisée.");
define('OGP_LANG_ticket_attachment_invalid_size', "%1 est plus gros que la taille de fichier autorisée. %2 maximum!");
define('OGP_LANG_ticket_max_file_elements', "Seulement un maximum de %1 champs de fichiers peuvent exister.");
define('OGP_LANG_ticket_attachment_multiple_files', "Une ou plusieurs entrées de fichier ont plusieurs fichiers sélectionnés.");
define('OGP_LANG_attachment_err_ini_size', "%s (%s) dépasse le paramètre 'upload_max_filesize'.");
define('OGP_LANG_attachment_err_partial', "%s a été seulement partiellement envoyé.");
define('OGP_LANG_attachment_err_no_tmp', "Aucun répertoire temporaire de PHP n'existe pour sauvegarder %s");
define('OGP_LANG_attachment_err_cant_write', "Impossible d'écrire %s sur le disque");
define('OGP_LANG_attachment_err_extension', "Une extension a arrêté l'envoi de %s. Regardez vos logs.");
define('OGP_LANG_attachment_too_large', "%s (%s) est plus gros que la taille maximale autorisée de %s!");
define('OGP_LANG_attachment_forbidden_type', "Le type de fichier de %s ne peut pas être envoyé.");
define('OGP_LANG_attachment_directory_not_writable', "Impossible de sauvegarder la pièce jointe. Le dossier de destination n'est pas autorisé en écriture.");
define('OGP_LANG_attachment_invalid_file_count', "La quantité de fichiers envoyés au serveur est incorrecte. Seulement un maximum de %s peut être envoyé");
define('OGP_LANG_ratings_enabled', "Notes");
define('OGP_LANG_ratings_enabled_info', "Défini si les réponses d'évaluation doivent être autorisées.");
define('OGP_LANG_attachments_enabled', "Pièces Jointes");
define('OGP_LANG_attachments_enabled_info', "Défini si les pièces jointes doivent être activées.");
define('OGP_LANG_attachment_max_size', "Taille Maximum des Fichiers");
define('OGP_LANG_attachment_max_size_info', "Défini la taille maximale des fichiers joints.");
define('OGP_LANG_attachment_limit', "Limite des Pièces Jointes");
define('OGP_LANG_attachment_limit_info', "Défini combien de fichiers peuvent être attachés à la fois. Indiquer 0 pour aucune limite.");
define('OGP_LANG_attachment_save_dir', "Emplacement des Pièces Jointes");
define('OGP_LANG_attachment_save_dir_info', "Défini où les pièces jointes doivent être envoyées. Idéalement, en dehors du dossier public_html ou alors avec l'accès direct bloqué.");
define('OGP_LANG_attachment_extensions', "Extensions des Pièces Jointes");
define('OGP_LANG_attachment_extensions_info', "Défini les extensions autorisées. Chaque extension doit être séparée par une virgule.");
define('OGP_LANG_show_php_ini', "Voir une estimation de la config PHP");
define('OGP_LANG_settings_errors_occured', "Les erreurs suivantes sont survenues lors de la tentative de mise à jour des paramètres - tout n'a pas été mis à jour!");
define('OGP_LANG_invalid_max_size', "Valeur incorrecte pour le paramètre Taille Maximum des Fichiers");
define('OGP_LANG_invalid_unit', "Unité invalide  pour le paramètre Taille Maximum des Fichiers. Unité attendue: KB, MB, GB, TB, ou PB.");
define('OGP_LANG_invalid_save_dir', "Le dossier de sauvegarde spécifié n'existe pas et ne peut pas être créé.");
define('OGP_LANG_invalid_save_dir_not_writable', "Le dossier de sauvegarde spécifié existe mais n'est pas autorisé en écriture..");
define('OGP_LANG_invalid_extensions', "Aucune extension de pièce jointe n'a été spécifiée.");
define('OGP_LANG_update_settings', "Enregistrer les Paramètres");
define('OGP_LANG_notifications_enabled', "Notifications");
define('OGP_LANG_notifications_enabled_info', "Permet à l&apos;utilisateur/admin de voir s&apos;il a un ticket en attente de réponse.");
