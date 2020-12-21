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

define('OGP_LANG_support_tickets', "Tickets de suporte");
define('OGP_LANG_ticket_subject', "Assunto");
define('OGP_LANG_ticket_status', "Andamento");
define('OGP_LANG_ticket_updated', "Ultima atualização");
define('OGP_LANG_ticket_options', "Opções");
define('OGP_LANG_viewing_ticket', "Visualizando Ticket");
define('OGP_LANG_ticket_not_found', "Os parâmetros de ticket fornecidos não correspondem a um ticket existente.");
define('OGP_LANG_ticket_cant_read', "Permissão insuficiente para visualizar o ticket.");
define('OGP_LANG_cant_view_ticket', "Impossível recuperar as informações do ticket.");
define('OGP_LANG_ticket_id', "Ticket ID");
define('OGP_LANG_service_id', "ID do serviço");
define('OGP_LANG_ticket_submitted', "Ticket Enviado");
define('OGP_LANG_submitter_info', "Informação do usuário");
define('OGP_LANG_name', "Nome");
define('OGP_LANG_ip', "IP");
define('OGP_LANG_role', "Função do usuário");
define('OGP_LANG_ticket_submit_response', "Enviar Resposta");
define('OGP_LANG_ticket_close', "Fechar");
define('OGP_LANG_no_ticket_replies', "Sem Resposta");
define('OGP_LANG_no_tickets_submitted', "Nenhum ticket foi enviado");
define('OGP_LANG_submit_ticket', "Enviar Ticket");
define('OGP_LANG_ticket_service', "Departamento");
define('OGP_LANG_ticket_message', "Mensagem");
define('OGP_LANG_ticket_errors_occured', "Os seguintes erros ocorreram ao enviar seu ticket");
define('OGP_LANG_no_ticket_subject', "Ticket sem título ");
define('OGP_LANG_invalid_ticket_subject_length', "Tamanho do título inválido (4 a 64 caracteres)");
define('OGP_LANG_invalid_home_selected', "Departamento selecionado inválido");
define('OGP_LANG_no_ticket_message', "O ticket não contem mensagem");
define('OGP_LANG_invalid_ticket_message_length', "Tamanho da mensagem inválido (mínimo de 4 caracteres)");
define('OGP_LANG_ticket_no_service', "Nenhum departamento foi selecionado.");
define('OGP_LANG_failed_to_open', "Falha ao abrir ticket.");
define('OGP_LANG_failed_to_reply', "Falha ao criar resposta ao ticket");
define('OGP_LANG_no_ticket_reply', "Nenhuma resposta fornecida ao ticket");
define('OGP_LANG_invalid_ticket_reply_length', "Comprimento de resposta de ticket inválido (mínimo de 4 caracteres)");
define('OGP_LANG_ticket_closed', "Ticket Fechado");
define('OGP_LANG_ticket_open', "Ticket Aberto");
define('OGP_LANG_ticket_admin_response', "Resposta do Administrador");
define('OGP_LANG_ticket_customer_response', "Resposta ao Cliente");
define('OGP_LANG_ticket_invalid_page_num', "Você tentou visualizar um número de página sem tickets!");
define('OGP_LANG_ticket_is_closed', "Este ticket está fechado. Você pode responder a este ticket para reabri-lo.");
define('OGP_LANG_reply', "Resposta");
define('OGP_LANG_invalid_rating', "A classificação recebida não é válida.");
define('OGP_LANG_successfully_rated_response', "Resposta avaliada com sucesso.");
define('OGP_LANG_failed_rating_response', "Falha ao avaliar a resposta.");
define('OGP_LANG_attachment_not_all_parameters_sent', "Nem todos os parâmetros foram enviados para baixar o arquivo.");
define('OGP_LANG_requested_attachment_missing', "O anexo solicitado não existe.");
define('OGP_LANG_requested_attachment_missing_db', "O anexo solicitado não existe na nossa base de dados.");
define('OGP_LANG_ratings_disabled', "As respostas de classificação não estão ativadas.");
define('OGP_LANG_attachments', "Anexos");
define('OGP_LANG_add_file_attachment', "Adicione mais");
define('OGP_LANG_attachment_size_info', "Cada arquivo selecionado pode ter no máximo%s");
define('OGP_LANG_attachment_file_size_info', "Um máximo de arquivo(s) %s  pode ser carregado, %s cada um.");
define('OGP_LANG_attachment_allowed_extensions_info', "Extensões de arquivos permitidas: %s");
define('OGP_LANG_ticket_fix_before_submitting', "Por favor, corrija os seguintes erros antes de enviar o ticket");
define('OGP_LANG_ticket_fix_before_replying', "Por favor, corrija os seguintes erros antes de responder ao ticket");
define('OGP_LANG_ticket_problem_with_attachments', "Houve um problema com o arquivo(s) que você anexou");
define('OGP_LANG_ticket_attachment_invalid_extension', "%1  não contém uma extensão permitida.");
define('OGP_LANG_ticket_attachment_invalid_size', "%1 é maior que o tamanho de arquivo permitido. %2 no máximo!");
define('OGP_LANG_ticket_max_file_elements', "Apenas um máximo de %1 entradas de arquivo podem existir.");
define('OGP_LANG_ticket_attachment_multiple_files', "Uma ou mais entradas de arquivo possuem vários arquivos selecionados.");
define('OGP_LANG_attachment_err_ini_size', "%s (%s) excede a configuração \"upload_max_filesize\".");
define('OGP_LANG_attachment_err_partial', "%s  foi apenas parcialmente carregado.");
define('OGP_LANG_attachment_err_no_tmp', "Nenhum diretório tmp existe para salvar %s");
define('OGP_LANG_attachment_err_cant_write', "Não é possível gravar %s no disco.");
define('OGP_LANG_attachment_err_extension', "Uma extensão parou o upload de %s. Revise seus logs.");
define('OGP_LANG_attachment_too_large', "%s (%s) é maior que o tamanho máximo permitido de %s!");
define('OGP_LANG_attachment_forbidden_type', " O tipo de arquivo %s não pode ser carregado.");
define('OGP_LANG_attachment_directory_not_writable', " Não é possível salvar os arquivos anexados. O diretório de salvamento especificado não é gravável.");
define('OGP_LANG_attachment_invalid_file_count', "A quantidade de arquivos enviados para o servidor era inválida. Apenas um máximo de %s pode ser carregado");
define('OGP_LANG_ratings_enabled', "Classificações");
define('OGP_LANG_ratings_enabled_info', "Defina se as respostas de classificação devem ser permitidas.");
define('OGP_LANG_attachments_enabled', "Anexos");
define('OGP_LANG_attachments_enabled_info', "Defina se o sistema de anexos deve ser ativado.");
define('OGP_LANG_attachment_max_size', "Tamanho máximo do arquivo");
define('OGP_LANG_attachment_max_size_info', "Define o tamanho máximo do arquivo para anexos.");
define('OGP_LANG_attachment_limit', "Limite de Anexos");
define('OGP_LANG_attachment_limit_info', "Define quantos arquivos podem ser anexados de uma só vez. 0 para nenhum limite.");
define('OGP_LANG_attachment_save_dir', "Local de Upload de Anexos");
define('OGP_LANG_attachment_save_dir_info', "Define onde os anexos devem ser enviados. Idealmente, fora da pasta public_html ou acesso direto bloqueado.");
define('OGP_LANG_attachment_extensions', "Extensões de Anexo");
define('OGP_LANG_attachment_extensions_info', "Define as extensões permitidas. Cada extensão deve ser separada por uma vírgula.");
define('OGP_LANG_show_php_ini', "Mostrar configurações INI estimadas");
define('OGP_LANG_settings_errors_occured', "Os seguintes erros ocorreram ao tentar atualizar as configurações - nem tudo foi atualizado!");
define('OGP_LANG_invalid_max_size', "Valor inválido para a configuração de tamanho máximo.");
define('OGP_LANG_invalid_unit', "Tipo de unidade inválido para configuração de tamanho máximo. Esperando KB, MB, GB, TB ou PB.");
define('OGP_LANG_invalid_save_dir', "O diretório de salvamento especificado não existe e não pode ser criado.");
define('OGP_LANG_invalid_save_dir_not_writable', "O diretório de salvamento especificado existe, mas não é gravável.");
define('OGP_LANG_invalid_extensions', "Nenhuma extensão de anexo foi especificada.");
define('OGP_LANG_update_settings', "Atualizar configurações");
define('OGP_LANG_notifications_enabled', "Notificações");
define('OGP_LANG_notifications_enabled_info', "Permitir que o usuário/administrador veja se recebeu um ticket aguardando resposta.");
