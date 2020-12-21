<?php

require 'include/ticket.php';
require 'include/TicketSettings.php';

function exec_ogp_module()
{
    global $db;

    $ticket = new ticket($db);
    
    $TicketSettings = (new TicketSettings($db))->get('attachment_save_dir');
    $saveDir = (substr($TicketSettings['attachment_save_dir'], -1) == '/' ? $TicketSettings['attachment_save_dir'] : $TicketSettings['attachment_save_dir'] . '/');

    $isAdmin = $db->isAdmin($_SESSION['user_id']);

    $id = (int)$_GET['id'];
    $tid = (int)$_GET['tid'];
    $uid = $_GET['uid'];

    if (empty($id) || empty($tid) || empty($uid)) {
        print_failure(get_lang('attachment_not_all_parameters_sent'));
        return;
    }

    if (!$ticket->exists($tid, $uid)) {
        print_failure(get_lang('ticket_not_found'));
        return;
    }

    if (!$isAdmin && !$ticket->authorized($_SESSION['user_id'], $tid, $uid)) {
        print_failure(get_lang('ticket_cant_read'));
        return;
    }

    $attachment = $ticket->getAttachmentById($id, $tid);

    if (!$attachment) {
        print_failure(get_lang('requested_attachment_missing_db'));
        return;
    }

    $onDiskName = $saveDir . $attachment['unique_name'];
    $originalName = $attachment['original_name'];

    if (!file_exists($onDiskName)) {
        print_failure(get_lang('requested_attachment_missing'));
        return;
    }

    $mime = new finfo(FILEINFO_MIME_TYPE);
    $encoding = new finfo(FILEINFO_MIME_ENCODING);

    header('Content-Type: '.$mime->file($onDiskName));
    header('Content-Transfer-Encoding: '.$mime->file($encoding));
    header('Content-disposition: attachment; filename="'.basename($originalName).'"');
    readfile($onDiskName);
}
