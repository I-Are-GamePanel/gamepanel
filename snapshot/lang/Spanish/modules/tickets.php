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

define('OGP_LANG_support_tickets', "Peticiones de soporte");
define('OGP_LANG_ticket_subject', "Asunto");
define('OGP_LANG_ticket_status', "Estado");
define('OGP_LANG_ticket_updated', "Ultima actualización");
define('OGP_LANG_ticket_options', "Opciones");
define('OGP_LANG_viewing_ticket', "Revisando petición");
define('OGP_LANG_ticket_not_found', "No se encontró ninguna petición de soporte que coincida con los parámetros introducidos.");
define('OGP_LANG_ticket_cant_read', "Permisos insuficientes");
define('OGP_LANG_cant_view_ticket', "No se puede recuperar la información.");
define('OGP_LANG_ticket_id', "ID de Petición");
define('OGP_LANG_service_id', "Servicio ID");
define('OGP_LANG_ticket_submitted', "Petición de soporte enviada");
define('OGP_LANG_submitter_info', "Información del remitente");
define('OGP_LANG_name', "Nombre");
define('OGP_LANG_ip', "IP");
define('OGP_LANG_role', "Rol de Usuario");
define('OGP_LANG_ticket_submit_response', "Enviar Respuesta");
define('OGP_LANG_ticket_close', "Cerrar");
define('OGP_LANG_no_ticket_replies', "No hay respuestas");
define('OGP_LANG_no_tickets_submitted', "No se han hecho peticiones de soporte");
define('OGP_LANG_submit_ticket', "Enviar Petición");
define('OGP_LANG_ticket_service', "Servicio");
define('OGP_LANG_ticket_message', "Mensaje");
define('OGP_LANG_ticket_errors_occured', "Los siguientes errores ocurrieron al enviar su petición");
define('OGP_LANG_no_ticket_subject', "No ha definido un asunto");
define('OGP_LANG_invalid_ticket_subject_length', "Longitud de Asunto inválida (4 a 64 caracteres)");
define('OGP_LANG_invalid_home_selected', "El Home seleccionado no es valido.");
define('OGP_LANG_no_ticket_message', "No ha introducido un mensaje.");
define('OGP_LANG_invalid_ticket_message_length', "Longitud de Mensaje inválida (Mínimo 4 caracteres)");
define('OGP_LANG_ticket_no_service', "No ha seleccionado un servicio.");
define('OGP_LANG_failed_to_open', "No se pudo abrir la petición.");
define('OGP_LANG_failed_to_reply', "No se pudo crear una respuesta.");
define('OGP_LANG_no_ticket_reply', "No proporcionó una respuesta");
define('OGP_LANG_invalid_ticket_reply_length', "Longitud de Respuesta inválida (Mínimo 4 caracteres)");
define('OGP_LANG_ticket_closed', "Caso Cerrado");
define('OGP_LANG_ticket_open', "Caso Abierto");
define('OGP_LANG_ticket_admin_response', "Respuesta del Administrador");
define('OGP_LANG_ticket_customer_response', "Respuesta del Cliente");
define('OGP_LANG_ticket_invalid_page_num', "Estás intentado ver un número de página que no contiene entradas!");
define('OGP_LANG_ticket_is_closed', "Caso cerrado. Responde a esta petición para reabrir el caso.");
define('OGP_LANG_reply', "Respuesta");
define('OGP_LANG_invalid_rating', "La puntuación introducida no es valida.");
define('OGP_LANG_successfully_rated_response', "Respuesta puntuada correctamente.");
define('OGP_LANG_failed_rating_response', "Hubo un error al puntuar la respuesta");
define('OGP_LANG_attachment_not_all_parameters_sent', "No se enviaron todos los parámetros necesarios para la descarga del archivo.");
define('OGP_LANG_requested_attachment_missing', "El archivo adjunto ya no existe en el servidor.");
define('OGP_LANG_requested_attachment_missing_db', "El archivo adjunto ya no existe en la base de datos.");
define('OGP_LANG_ratings_disabled', "La puntuación de respuestas está desactivada.");
define('OGP_LANG_attachments', "Archivos adjuntos");
define('OGP_LANG_add_file_attachment', "Añadir mas");
define('OGP_LANG_attachment_size_info', "Podría tener un máximo de %s archivos seleccionados.");
define('OGP_LANG_attachment_file_size_info', "Se podrían subir un máximo de %s archivos, %s cada uno.");
define('OGP_LANG_attachment_allowed_extensions_info', "Extensiones de archivo permitidas: %s");
define('OGP_LANG_ticket_fix_before_submitting', "Por favor subsane los errores a continuación antes de enviar su petición");
define('OGP_LANG_ticket_fix_before_replying', "Por favor subsane los errores a continuación antes de enviar su respuesta");
define('OGP_LANG_ticket_problem_with_attachments', "Hubo un problema con los archivos adjuntos");
define('OGP_LANG_ticket_attachment_invalid_extension', "%1 la extensión del archivo no está permitida.");
define('OGP_LANG_ticket_attachment_invalid_size', "%1 es mayor que el tamaño máximo permitido. %2 máximo!");
define('OGP_LANG_ticket_max_file_elements', "Solo pueden existir un máximo de %1 entradas de archivo.");
define('OGP_LANG_ticket_attachment_multiple_files', "Una o mas entradas tienen múltiples archivos seleccionados");
define('OGP_LANG_attachment_err_ini_size', "%s (%s) excede la configuración PHP para 'upload_max_filesize'.");
define('OGP_LANG_attachment_err_partial', "%s se subió parcialmente.");
define('OGP_LANG_attachment_err_no_tmp', "No existe la carpeta de guardado temporal %s");
define('OGP_LANG_attachment_err_cant_write', "Imposible escribir %s en el disco.");
define('OGP_LANG_attachment_err_extension', "Una extensión PHP detuvo la subida del fichero %s. Revise los logs.");
define('OGP_LANG_attachment_too_large', "%s (%s) es mayor que el tamaño máximo definido de %s!");
define('OGP_LANG_attachment_forbidden_type', "El tipo de archivo %s no se pudo subir al servidor.");
define('OGP_LANG_attachment_directory_not_writable', "No fue posible guardar los archivos adjuntos. No tiene acceso de escritura en el directorio de guardado que ha especificado.");
define('OGP_LANG_attachment_invalid_file_count', "El número de archivos enviados al servidor no es valido. Solo se pueden subir un máximo de %s archivos.");
define('OGP_LANG_ratings_enabled', "Puntuaciones");
define('OGP_LANG_ratings_enabled_info', "Determina si la puntuación de respuestas esta habilitada.");
define('OGP_LANG_attachments_enabled', "Archivos adjuntos");
define('OGP_LANG_attachments_enabled_info', "Determina si el sistema de archivos adjuntos esta disponible");
define('OGP_LANG_attachment_max_size', "Tamaño máximo de archivo");
define('OGP_LANG_attachment_max_size_info', "Determina el tamaño máximo del total de archivos adjuntos en un solo mensaje.");
define('OGP_LANG_attachment_limit', "Límite de archivos adjunos");
define('OGP_LANG_attachment_limit_info', "Determina cuantos archivos adjuntos puede tener un mensaje, 0 deshabilitar el límite.");
define('OGP_LANG_attachment_save_dir', "Ubicación de los archivos subidos");
define('OGP_LANG_attachment_save_dir_info', "Determina donde deberían almacenarse los archivos adjuntos, preferentemente fuera de la carpeta publica del servidor web.");
define('OGP_LANG_attachment_extensions', "Extensiones de archivo adjunto permitidas");
define('OGP_LANG_attachment_extensions_info', "Determina que extensiones de archivo están permitidas para los archivos adjuntos. Debe separarlas mediante el uso de comas.");
define('OGP_LANG_show_php_ini', "Mostrar configuraciones de inicialización estimadas");
define('OGP_LANG_settings_errors_occured', "Los siguientes errores ocurrieron al intentar actualizar las configuraciones - no todo ha sido actualizado!");
define('OGP_LANG_invalid_max_size', "Valor invalido para Max Size.");
define('OGP_LANG_invalid_unit', "Tipo de unidad invalida usada para Max Size. Se esperaba KB, MB, GB o PB.");
define('OGP_LANG_invalid_save_dir', "El directorio de guardado especificado no existe y tampoco pudo crearse.");
define('OGP_LANG_invalid_save_dir_not_writable', "El directorio de guardado especificado existe, pero no tiene permiso de escritura.");
define('OGP_LANG_invalid_extensions', "No se especificaron extensiones de archivo.");
define('OGP_LANG_update_settings', "Actualizar Configuración");
define('OGP_LANG_notifications_enabled', "Notificaciones");
define('OGP_LANG_notifications_enabled_info', "Mostrar una notificación en el botón del menu si el usuario o el administrador tiene peticiones de soporte esperando por una respuesta.");
