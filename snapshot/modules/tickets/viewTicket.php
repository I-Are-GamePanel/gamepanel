<?php

require 'include/ticket.php';
require 'include/Attachments.php';
require 'include/TicketSettings.php';

require 'include/functions.php';

function exec_ogp_module()
{
    global $db, $view;

    if (isset($_SESSION['ticket'])) {
        unset($_SESSION['ticket']);
    }

    $ticket = new Ticket($db);
    $TicketSettings = new TicketSettings($db);

    $isAdmin = $db->isAdmin($_SESSION['user_id']);
    $attachmentSettings = $TicketSettings->get(array('attachments_enabled', 'attachment_save_dir', 'attachment_limit', 'attachment_max_size', 'attachment_extensions', 'ratings_enabled'));

    echo '<h2>'.get_lang('viewing_ticket').'</h2>';

    $tid = (int)$_GET['tid'];
    $uid = $_GET['uid'];
    
    $ticketData = $ticket->getTicket($tid, $uid);

    if (!$ticket->exists($tid, $uid)) {
        print_failure(get_lang('ticket_not_found'));
        $view->refresh("?m=tickets");

        return;
    }

    if (!$isAdmin && !$ticket->authorized($_SESSION['user_id'], $tid, $uid)) {
        print_failure(get_lang('ticket_cant_read'));
        $view->refresh("?m=tickets");

        return;
    }

    if (!$ticketData) {
        print_failure(get_lang('cant_view_ticket'));
        $view->refresh("?m=tickets");

        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $attachments = new Attachments(
            $db,
            $_FILES['ticket_file'],
            $attachmentSettings['attachment_save_dir'],
            $attachmentSettings['attachment_limit'],
            $attachmentSettings['attachment_max_size'],
            explode(', ', $attachmentSettings['attachment_extensions'])
        );

        if (isset($_POST['ticket_close'])) {
            $ticket->updateStatus($tid, $uid, 0);
            $view->refresh("?m=tickets&p=viewticket&tid=".$tid."&uid=".$uid, 0);
            return;
        }

        if (isset($_POST['ticket_submit_response'])) {
            $_POST = array_map('trim', $_POST);
            $_SESSION['ticketReply'] = strip_real_escape_string($_POST['reply_content']);

            $errors     = array();
            $fileErrors = array();

            if (empty($_POST['reply_content'])) {
                $errors[] = get_lang('no_ticket_reply');
            } elseif (strlen($_POST['reply_content']) < 4) {
                $errors[] = get_lang('invalid_ticket_reply_length');
            }

            if ($attachments->checkPath() === false && $attachmentSettings['attachments_enabled']) {
                $fileErrors[] = get_lang('attachment_directory_not_writable');
            }

            if ($attachments->validAttachmentCount() === false && $attachmentSettings['attachments_enabled']) {
                $fileErrors[] = get_lang_f('attachment_invalid_file_count', $attachmentSettings['attachment_limit']);
            }

            if (empty($errors)) {
                $reply = $ticket->message($tid, $_SESSION['user_id'], getClientIPAddress(), strip_real_escape_string($_POST['reply_content']), $isAdmin, $uid);
                
                if (!$reply) {
                    echo ticketErrors(array(get_lang('failed_to_reply')));
                    $view->refresh("?m=tickets&p=submitticket", 60);
                    return;
                }

                if (isset($_SESSION['ticketReply'])) {
                    unset($_SESSION['ticketReply']);
                }

                if ($attachmentSettings['attachments_enabled']) {
                    // Validate the uploaded files if specified path exists and is writable. and if the amount of files is valid.
                    // if any files fail to validate, then only save/move the ones which validated successfully and show an error for the ones which didn't.
                    if (empty($fileErrors)) {
                        $validator = $attachments->validate();
                        $fileErrors[] = $validator->getErrors();
                        $attachments->save($tid, $reply);
                    }

                    setcookie('fileErrors', json_encode(array('uid' => $uid, 'fileErrors' => $fileErrors)), time() + 86400, '/');
                }

                $view->refresh("?m=tickets&p=viewticket&tid=".$tid."&uid=".$uid, 0);
                return;
            } else {
                echo ticketErrors($errors);
                $view->refresh("?m=tickets&p=viewticket&tid=".$tid."&uid=".$uid, 60);
                return;
            }
        }
    }

    echo '<div id="jsErrorBox">'. ticketErrors() .'</div>';
    echo ticketHeader($ticketData);

    if ($ticketData['status'] == 0) {
        echo '<div class="ticket_closed">'.get_lang('ticket_is_closed').'</div>';

        echo '<div class="ticket_reply_notice">';
        echo '<div class="left" id="toggleNoticeMessage">'.get_lang('reply').'</div>';
        echo '<div class="right" id="toggleNoticeIcon">+</div>';
        echo '<div class="clear"></div>';
        echo '</div>';
    }

    echo '<div class="ticket_ReplyBox status_'.ticketCodeToName($ticketData['status'], true).'">
        <form method="POST" enctype="multipart/form-data">
            <textarea name="reply_content" id="messageBox" style="width:100%;" rows="12">'.(isset($_SESSION['ticketReply']) ? $_SESSION['ticketReply'] : '').'</textarea>';

            if ($attachmentSettings['attachments_enabled']) {
                echo attachmentForm();
            }

            echo '<input type="submit" id="submit" class="ticket_button" name="ticket_submit_response" value="'. get_lang('ticket_submit_response') . '">
        '.($ticketData['status'] != 0 ? '<input type="submit" class="ticket_button" name="ticket_close" value="'. get_lang('ticket_close') . '">' : '').'
        </form>
    </div>';

    if (!empty($ticketData['messages'])) {
        echo '<div class="replyContainer">';
        foreach ($ticketData['messages'] as $message) {
            echo ticketMessage($message, $uid, $isAdmin, $attachmentSettings['ratings_enabled']);
        }
        echo '</div>';
    }

    if (empty($ticketData['messages']) && $ticketData['status'] != 0) {
        echo '<div class="no_ticket_replies">'.get_lang('no_ticket_replies').'</div>';
    }

    require 'js/javascript_vars.php';
?>
<script src="modules/tickets/js/helpers.js"></script>
<script src="modules/tickets/js/ticket.js"></script>
<script src="modules/tickets/js/rating.js"></script>

<?php
}