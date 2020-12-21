<?php

require 'include/ticket.php';
require 'include/TicketSettings.php';

function exec_ogp_module()
{
    global $db;

    $ticket = new Ticket($db);
    $TicketSettings = new TicketSettings($db);
    $notificationsEnabled = $TicketSettings->get('notifications_enabled');

    if ($notificationsEnabled['notifications_enabled']) {
        $isAdmin = $db->isAdmin($_SESSION['user_id']);
        $status = $isAdmin ? 3 : 2;
        $ticketOwner = (!$isAdmin ? $_SESSION['user_id'] : null);
        
        echo json_encode(
            array('notificationCount' => $ticket->notificationCount($ticketOwner, $status)
            )
        );
    }
}