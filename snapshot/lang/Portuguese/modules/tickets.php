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
define('OGP_LANG_ticket_status', "Estado");
define('OGP_LANG_ticket_updated', "Ultima actualização");
define('OGP_LANG_ticket_options', "Opções");
define('OGP_LANG_viewing_ticket', "Ver Ticket");
define('OGP_LANG_ticket_not_found', "Os parâmetros de ticket fornecidos não correspondem a um ticket existente.");
define('OGP_LANG_ticket_cant_read', "Permissão insuficiente para ver o ticket.");
define('OGP_LANG_cant_view_ticket', "Não é possível recuperar as informações do ticket.");
define('OGP_LANG_ticket_id', "Ticket ID");
define('OGP_LANG_service_id', "ID do serviço");
define('OGP_LANG_ticket_submitted', "Ticket Enviado");
define('OGP_LANG_submitter_info', "Informação do remetente");
define('OGP_LANG_name', "Nome");
define('OGP_LANG_ip', "IP");
define('OGP_LANG_role', "Regras do Usuário ");
define('OGP_LANG_ticket_submit_response', "Enviar resposta");
define('OGP_LANG_ticket_close', "Fechar");
define('OGP_LANG_no_ticket_replies', "Ticket sem resposta");
define('OGP_LANG_no_tickets_submitted', "Você ainda não enviou nenhum Tiket de Suporte");
define('OGP_LANG_submit_ticket', "Enviar Ticket");
define('OGP_LANG_ticket_service', "Serviço");
define('OGP_LANG_ticket_message', "Messagem");
define('OGP_LANG_ticket_errors_occured', "Ocorreram os seguintes erros ao enviar o seu ticket");
define('OGP_LANG_no_ticket_subject', "Ticket  Sem Assunto");
define('OGP_LANG_invalid_ticket_subject_length', "Comprimento do campo assunto inválido (4 a 64 caracteres)");
define('OGP_LANG_invalid_home_selected', "Selecção de directório invalido");
define('OGP_LANG_no_ticket_message', "Nenhuma mensagem de Ticket  foi fornecida");
define('OGP_LANG_invalid_ticket_message_length', "Comprimento da mensagem do ticket inválido (mínimo 4 caracteres)");
define('OGP_LANG_ticket_no_service', "Nenhum serviço seleccionado para este ticket.");
define('OGP_LANG_failed_to_open', "Falha ao abrir o ticket.");
define('OGP_LANG_failed_to_reply', "Falha ao criar resposta ao ticket.");
define('OGP_LANG_no_ticket_reply', "Nenhuma resposta de ticket fornecida");
define('OGP_LANG_invalid_ticket_reply_length', "Comprimento de resposta de ticket inválido (mínimo de 4 caracteres)");
define('OGP_LANG_ticket_closed', "Ticket Fechado");
define('OGP_LANG_ticket_open', "Ticket Aberto");
define('OGP_LANG_ticket_admin_response', "Resposta de Administrador");
define('OGP_LANG_ticket_customer_response', "Resposta do Cliente");
define('OGP_LANG_ticket_invalid_page_num', "Você tentou ver um número de página sem bilhetes!");
define('OGP_LANG_ticket_is_closed', "Este ticket encontra-se fechado. Você pode responder ao mesmo para reabri-lo.");
define('OGP_LANG_reply', "Responder");
define('OGP_LANG_invalid_rating', "Desculpe, mas a classificação recebida não é válida.");
define('OGP_LANG_successfully_rated_response', "Resposta avaliada com sucesso.");
define('OGP_LANG_failed_rating_response', "Desculpe, mas ocorreu uma falha ao avaliar a resposta.");
define('OGP_LANG_attachment_not_all_parameters_sent', "Nem todos os parâmetros foram enviados correctamente para baixar o arquivo.");
define('OGP_LANG_requested_attachment_missing', "O anexo solicitado não existe.");
define('OGP_LANG_requested_attachment_missing_db', "O anexo solicitado não existe na base de dados.");
define('OGP_LANG_ratings_disabled', "As respostas de avaliação não estão habilitadas.");
define('OGP_LANG_attachments', "Anexos");
define('OGP_LANG_add_file_attachment', "Adicione mais");
define('OGP_LANG_attachment_size_info', "Cada arquivo seleccionado pode conter o máximo de %s");
define('OGP_LANG_attachment_file_size_info', "O máximo de %s arquivo(s) podem ser carregados, %s a cada um.");
define('OGP_LANG_attachment_allowed_extensions_info', "Extensões de arquivo permitidas: %s");
define('OGP_LANG_ticket_fix_before_submitting', "Corrija os seguintes erros antes de enviar o ticket");
define('OGP_LANG_ticket_fix_before_replying', "Corrija os seguintes erros antes de responder ao ticket");
define('OGP_LANG_ticket_problem_with_attachments', "Ocorreu um problema com o(s) arquivo(s) que você anexou");
define('OGP_LANG_ticket_attachment_invalid_extension', "%1 não contém uma extensão permitida.");
define('OGP_LANG_ticket_attachment_invalid_size', "%1 é maior que o tamanho do arquivo permitido. %2 máximo!");
define('OGP_LANG_ticket_max_file_elements', "Somente um máximo de entradas de arquivo %1 podem existir.");
define('OGP_LANG_ticket_attachment_multiple_files', "Uma ou mais entradas de arquivos têm vários arquivos seleccionados.");
define('OGP_LANG_attachment_err_ini_size', "%s (%s) excede o 'upload_max_filesize' das configurações permitidas");
define('OGP_LANG_attachment_err_partial', "%s foi parcialmente carregado.");
define('OGP_LANG_attachment_err_no_tmp', "Não existe nenhum directório \"tmp\" para salvar %s");
define('OGP_LANG_attachment_err_cant_write', "Impossibilitado de escrever %s para o disco.");
define('OGP_LANG_attachment_err_extension', "Uma extensão interrompeu o upload de %s. Reveja os logs para perceber melhor o que aconteceu.");
define('OGP_LANG_attachment_too_large', "%s (%s) é maior que o tamanho máximo permitido de %s!");
define('OGP_LANG_attachment_forbidden_type', "O tipo de arquivo de %s não pode ser carregado.");
define('OGP_LANG_attachment_directory_not_writable', "Não foi possível salvar os arquivos anexados. O directório que permite guardar/salvar os seus arquivos especificados pode não conter as permissões correctas para salvar/guardar/modificar.");
define('OGP_LANG_attachment_invalid_file_count', "A quantidade de arquivos enviados para o servidor foi inválida. Apenas um máximo de %s pode ser carregado");
define('OGP_LANG_ratings_enabled', "Classificações");
define('OGP_LANG_ratings_enabled_info', "Defina se as respostas de classificação devem ser permitidas.");
define('OGP_LANG_attachments_enabled', "Anexos");
define('OGP_LANG_attachments_enabled_info', "Defina se o sistema de anexos deve ser ativado.");
define('OGP_LANG_attachment_max_size', "Tamanho máximo do arquivos");
define('OGP_LANG_attachment_max_size_info', "Define o tamanho máximo do arquivo para anexos.");
define('OGP_LANG_attachment_limit', "Limite de Anexos");
define('OGP_LANG_attachment_limit_info', "Define quantos arquivos podem ser anexados ao mesmo tempo. 0 sem limite.");
define('OGP_LANG_attachment_save_dir', "Localização do upload do anexo");
define('OGP_LANG_attachment_save_dir_info', "Define onde os anexos devem ser carregados. Idealmente, fora da pasta public_html ou acesso directo bloqueado.");
define('OGP_LANG_attachment_extensions', "Extensões de anexos");
define('OGP_LANG_attachment_extensions_info', "Define as extensões permitidas. Cada extensão deve ser separada por uma vírgula \" , \".");
define('OGP_LANG_show_php_ini', "Mostrar configurações INI estimadas");
define('OGP_LANG_settings_errors_occured', "Ocorreram os seguintes erros ao tentar actualizar as configurações - nem tudo foi actualizado!");
define('OGP_LANG_invalid_max_size', "Valor inválido para configuração de tamanho máximo.");
define('OGP_LANG_invalid_unit', "Tipo de unidade inválido para as configurações de tamanho Máximo. Esperando KB, MB, GB, TB ou PB.");
define('OGP_LANG_invalid_save_dir', "O directório de salvação especificado não existe e não pode ser criado.");
define('OGP_LANG_invalid_save_dir_not_writable', "O directório de salvação especificado existe, mas não pode ser gravado.");
define('OGP_LANG_invalid_extensions', "Nenhuma extensão de anexo foi especificada.");
define('OGP_LANG_update_settings', "Actualizar configurações");
define('OGP_LANG_notifications_enabled', "Notificações");
define('OGP_LANG_notifications_enabled_info', "Permitir que os usuário/administrador veja se recebeu um ticket a aguardar uma resposta.");
