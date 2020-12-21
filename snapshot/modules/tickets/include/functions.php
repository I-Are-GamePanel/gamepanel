<?php

function ticketHeader($info)
{
    $created = new DateTime($info['created_at']);
    $updated = new DateTime($info['last_updated']);

    return '<div class="divTable">
    <div class="divTableBody">
        <div class="divTableRow">
            <div class="divTableCell infoblock_ticket">'.get_lang('ticket_id').'</div>
            <div class="divTableCell contentblock_ticket">#'.$info['tid'].' - '.$info['uid'] .'</div>
        </div>
        <div class="divTableRow">
            <div class="divTableCell infoblock_ticket">'.get_lang('service_id').'</div>
            <div class="divTableCell contentblock_ticket">'.((int)$info['service_id'] === 0 ? '<i>'.get_lang('ticket_no_service').'</i>' :
                                        '<a href="?m=user_games&p=edit&home_id='.(int)$info['service_id'].'">#'.(int)$info['service_id'].'</a>'). '</div>
        </div>
        <div class="divTableRow">
            <div class="divTableCell infoblock_ticket">'.get_lang('ticket_subject').'</div>
            <div class="divTableCell contentblock_ticket">'.$info['subject'].'</div>
        </div>
        <div class="divTableRow">
            <div class="divTableCell infoblock_ticket">'.get_lang('ticket_submitted').'</div>
            <div class="divTableCell contentblock_ticket">'.$created->format('jS M Y (H:i)').'</div>
        </div>
        <div class="divTableRow">
            <div class="divTableCell infoblock_ticket">'.get_lang('ticket_updated').'</div>
            <div class="divTableCell contentblock_ticket">'.$updated->format('jS M Y (H:i)').'</div>
        </div>
        <div class="divTableRow">
            <div class="divTableCell infoblock_ticket">'.get_lang('ticket_status').'</div>
            <div class="divTableCell contentblock_ticket">'.ticketCodeToName($info['status']).'</div>
        </div>
        <div class="divTableRow">
            <div class="divTableCell infoblock_ticket">'.get_lang('submitter_info').'</div>
            <div class="divTableCell contentblock_ticket">'.get_lang('username').': <a href="?m=user_admin&p=edit_user&user_id='. $info['user_id'] .'">'. $info['users_login'] .'</a> - 
            '. (!empty($info['users_fname']) ? get_lang('name') . ': ' . htmlentities($info['users_fname']) . (!empty($info['users_lname']) ? ' '.htmlentities($info['users_lname']).' - ' : '') : '') .
            get_lang('ip') . ': '. inet_ntop($info['user_ip']) .' - '.get_lang('role') .': '. ucfirst($info['users_role']).'
            </div>
        </div>
    </div>
</div>';
}

function ticketMessage($messageData, $uid, $loggedInAdmin = false, $ratingsEnabled)
{
    $date = new DateTime($messageData['date']);
    $tid = $messageData['ticket_id'];
    $rating = $messageData['rating'];

    $class = 'user';
    
    if (isset($messageData['is_admin'])) {
        $class = $messageData['is_admin'] == 1 ? 'admin' : 'user';
    }

    $replyBox = '<div class="ticket_reply '. $class .'">
    <div class="date">
        '.$date->format('jS M Y (H:i)').'
    </div>
    <div class="'. $class .'">
        <span class="name">
            <a href="?m=user_admin&p=edit_user&user_id='.$messageData['user_id'].'">'. htmlentities($messageData['users_login']) .'</a> ' .
                    (!empty($messageData['users_fname']) ? htmlentities($messageData['users_fname']) . (!empty($messageData['users_lname']) ? ' '.htmlentities($messageData['users_lname']) : '') : '') .'
        </span>
        <span class="type">
            '.ucfirst($messageData['users_role']).'
        </span>
    </div>
    <div class="message">'.nl2br(htmlentities($messageData['message'])).'</div>';

    $replyBox .= '<div class="ticket_footer">';

    $replyBox .= '<div class="footer_row">';

    if ($messageData['users_role'] !== 'admin' || $loggedInAdmin) {
        $replyBox .= '<div class="left">'.get_lang('ip').': '.inet_ntop($messageData['user_ip']).'</div>';
    }

    if ($messageData['users_role'] == 'admin' && $ratingsEnabled) {
        $replyBox .= '<div class="right rateResponse" data-tid="'. $tid .'" data-uid="'. $uid .'" data-reply-id="'. $messageData['reply_id'] .'" data-rating="'. $rating .'"></div>';
    }

    $replyBox .= '<div class="clear"></div>';
    $replyBox .= '</div>'; // footer_row

    if (isset($messageData['attachments'])) {
        $replyBox .= '<div class="footer_row attachmentContainer">';

        $replyBox .= '<div class="left attachmentHeader">'. get_lang('attachments') .'</div>';
        $replyBox .= '<div class="clear"></div>';
        $replyBox .= '<div class="left attachmentList">';

        $attachmentList = '';
        foreach ($messageData['attachments'] as $attachment) {
            $attachmentList .= '<a href="#" class="downloadAttachmentLink" data-id="'. $attachment['attachment_id'] .'" data-tid="'. $tid .'" data-uid="'. $uid .'">'. htmlentities($attachment['original_name']) .'</a>, ';
        }

        $replyBox .= rtrim($attachmentList, ', ');
        $replyBox .= '</div>'; //left
        $replyBox .= '<div class="clear"></div>';

        $replyBox .= '</div>'; //footer row.
    }

    $replyBox .= '</div>'; // ticket_footer
    $replyBox .= '</div>'; // ./div :: ticket_reply $class

    return $replyBox;
}

function ticketErrors($errors = array(), $header = '')
{
    $header = empty($header) ? get_lang('ticket_errors_occured') . ':' : $header;
    $return = '<div class="ticketErrorHolder">
    <p class="failure" id="errorHeader">'. $header .'</p>
    <ul class="ticketErrorList">';
    foreach ($errors as $error) {
        $return .= '<li class="ticketError">' . $error . '</li>';
    }
    $return .= '</ul>
    </div>';

    return $return;
}

function ticketCodeToName($code, $css = false)
{
    $codes = array(
        'ticket_closed',
        'ticket_open',
        'ticket_admin_response',
        'ticket_customer_response',
    );
    
    return $css ? $codes[$code] : get_lang($codes[$code]);
}

function attachmentForm()
{
    $html = '
            <div class="attachment_container">
                <div class="attachment_header">'. get_lang('attachments') .'</div>

                <div class="attachment_add">
                    <button id="add_file_attachment">'. get_lang('add_file_attachment') .'</button>
                </div>

                <div class="attachment_inputs">
                    <input type="file" name="ticket_file[]">
                </div>

                <div class="attachment_info">
                    <div id="file_size_info"></div>
                    <div id="extension_info"></div>
                </div>
            </div>
    ';

    return $html;
}

function bytesTo($bytes)
{
    if ($bytes == 0) {
        return '0.00 B';
    }

    $s = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    $e = floor(log($bytes, 1024));

    return round($bytes / pow(1024, $e), 2) . $s[$e];
}

function toBytes($from)
{
    $number = substr($from, 0, -2);
    switch (strtoupper(substr($from, -2))) {
        case "KB":
            return $number*1024;
        case "MB":
            return $number*pow(1024, 2);
        case "GB":
            return $number*pow(1024, 3);
        case "TB":
            return $number*pow(1024, 4);
        case "PB":
            return $number*pow(1024, 5);
        default:
            return $from;
    }
}

function splitExtensions($extensions, $delimiter = ',')
{
    $extArr = explode($delimiter, $extensions);
    $extList = '';
        
    foreach ($extArr as $ext) {
        if (empty($ext)) {
            continue;
        }
            
        $extList .= str_replace(array('.', ' '), '', $ext) . $delimiter . ' ';
    }
        
    return rtrim($extList, $delimiter . ' ');
}