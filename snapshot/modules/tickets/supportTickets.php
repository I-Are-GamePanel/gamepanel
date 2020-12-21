<?php

require 'include/ticket.php';
require 'include/functions.php';

function exec_ogp_module()
{
    global $db, $loggedInUserInfo;

    if (isset($_SESSION['ticket'])) {
        unset($_SESSION['ticket']);
    }
    if (isset($_SESSION['ticketReply'])) {
        unset($_SESSION['ticketReply']);
    }

    $page    = (isset($_GET['page']) && (int)$_GET['page'] > 0) ? (int)$_GET['page'] : 1;
    $limit   = (isset($_GET['limit']) && (int)$_GET['limit'] > 0) ? (int)$_GET['limit'] : 10;

    if (!empty($loggedInUserInfo['users_page_limit']) && empty($_GET['limit'])) {
        $limit = $loggedInUserInfo['users_page_limit'];
    }

    $ticket = new Ticket($db);
    $isAdmin = $db->isAdmin($_SESSION['user_id']);

    $ticketOwner = (!$isAdmin ? $_SESSION['user_id'] : null);
    $ticketCount = $ticket->count($ticketOwner);
    $tickets = $ticket->tickets($ticketOwner, $page, $limit);

    echo '<h2>'.get_lang('support_tickets').'</h2>';

    echo '<div class="ticketOptionLinks">
        <a href="?m=tickets&p=submitticket">'.get_lang('submit_ticket').'</a>
    </div>';

    if ($tickets !== false && $ticketCount > 0) {
        echo '<table class="ticketListTable" style="width:100%;">';
        echo '<tr>';
        echo '<th>'.get_lang('ticket_subject').'</th>';
        echo '<th>'.get_lang('ticket_status').'</th>';
        echo '<th>'.get_lang('ticket_updated').'</th>';
        echo '</tr>';
        
        foreach ($tickets as $t) {
            $date = new DateTime($t['last_updated']);
            echo '<tr class="ticketRow '.ticketCodeToName($t['status'], true).'">
                <td><a href="?m=tickets&p=viewticket&tid='.$t['tid'].'&uid='.$t['uid'].'">'. htmlentities($t['subject']) .'</a></td>
                <td>'. ticketCodeToName($t['status']) .'</td>
                <td>'. $date->format('jS M Y (H:i)') .'</td>
            </tr>';
        }

        echo '</table>';

        echo '<div class="ticketPagination">'.paginationPages($ticketCount, $page, $limit, '?m=tickets&limit='.$limit.'&page=', 3, 'Tickets').'</div>';
    } else {
        if ($ticketCount > 0) {
            echo '<div class="no_tickets">' . get_lang('ticket_invalid_page_num') . '</div>';
        } else {
            echo '<div class="no_tickets">' . get_lang('no_tickets_submitted') . '</div>';
        }
    }
}