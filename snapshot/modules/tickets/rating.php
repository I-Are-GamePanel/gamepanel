<?php

require 'include/ticket.php';
require 'include/TicketSettings.php';

function exec_ogp_module()
{
    global $db, $view;

    $ticket = new Ticket($db);
    $TicketSettings = (new TicketSettings($db))->get('ratings_enabled');

    $isAdmin = $db->isAdmin($_SESSION['user_id']);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tid = (int)$_POST['tid'];
        $uid = $_POST['uid'];
        $reply_id = (int)$_POST['reply_id'];
        $validRatings = range(1, 5);
        
        if (is_numeric($_POST['rating']) && in_array($_POST['rating'], $validRatings)) {
            $rating = (int)$_POST['rating'];
        } else {
            $rating = 0;
        }

        if (!$TicketSettings['ratings_enabled']) {
            echo json_encode(array('message' => get_lang('ratings_disabled')));
            return;
        }

        if (!$ticket->exists($tid, $uid)) {
            echo json_encode(array('message' => get_lang('ticket_not_found')));
            return;
        }

        if (!$ticket->authorized($_SESSION['user_id'], $tid, $uid)) {
            echo json_encode(array('message' => get_lang('ticket_cant_read')));
            return;
        }


        if ($rating == 0) {
            echo json_encode(array('message' => get_lang('invalid_rating')));
            return;
        }

        if ($ticket->setRating($tid, $reply_id, $rating)) {
            echo json_encode(array('message' => get_lang('successfully_rated_response')));
        } else {
            echo json_encode(array('message' => get_lang('failed_rating_response')));
        }
    }

    $view->refresh("?m=tickets", 0);
}