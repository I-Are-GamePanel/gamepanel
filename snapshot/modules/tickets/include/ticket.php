<?php

class Ticket
{
    private $db;

    public function __construct(OGPDatabase $db)
    {
        $this->db = $db;
    }

    public function tickets($ticketsFor = null, $page = 1, $limit = 10)
    {
        $limitStart = ((int)($page - 1) * $limit);

        $query = "SELECT a.tid, a.uid, a.user_id, a.parent_id, a.subject, a.created_at, a.last_updated, a.status, a.assigned_to
                    FROM OGP_DB_PREFIXtickets a ";

        if ($ticketsFor !== null) {
            $query .= "WHERE a.user_id = ".(int)$ticketsFor." OR a.parent_id = ".(int)$ticketsFor." ";

            if ($this->db->isSubUser($ticketsFor)) {
                $result = $this->db->resultQuery("SELECT users_parent FROM OGP_DB_PREFIXusers WHERE user_id = ".(int)$ticketsFor);
                $query .= "OR a.parent_id = ".(int)$result[0]['users_parent']." ";
            }
        }

        $query .= "ORDER BY a.last_updated DESC ";
        $query .= "LIMIT $limitStart, ".(int)$limit;

        return $this->db->resultQuery($query);
    }

    public function count($ticketsFor = null)
    {
        $query = "SELECT COUNT(1) as ticketCount FROM OGP_DB_PREFIXtickets a ";

        if ($ticketsFor !== null) {
            $query .= "WHERE a.user_id = ".(int)$ticketsFor." OR a.parent_id = ".(int)$ticketsFor." ";
            
            if ($this->db->isSubUser($ticketsFor)) {
                $result = $this->db->resultQuery("SELECT users_parent FROM OGP_DB_PREFIXusers WHERE user_id = ".(int)$ticketsFor);
                $query .= "OR a.parent_id = ".(int)$result[0]['users_parent']." ";
            }
        }

        $result = $this->db->resultQuery($query);
        return (!is_array($result) ? 0 : $result[0]['ticketCount']);
    }

    public function notificationCount($ticketsFor = null, $status = 0)
    {
        $query = "SELECT COUNT(1) as ticketCount FROM OGP_DB_PREFIXtickets a WHERE a.status = ".(int)$status." ";

        if ($ticketsFor !== null) {
            $query .= "AND (a.user_id = ".(int)$ticketsFor." OR a.parent_id = ".(int)$ticketsFor." ";
            
            if ($this->db->isSubUser($ticketsFor)) {
                $result = $this->db->resultQuery("SELECT users_parent FROM OGP_DB_PREFIXusers WHERE user_id = ".(int)$ticketsFor);
                $query .= "OR a.parent_id = ".(int)$result[0]['users_parent'].")";
            } else {
                $query .= ")";
            }
        }
        
        $result = $this->db->resultQuery($query);
        return (!is_array($result) ? 0 : $result[0]['ticketCount']);
    }

    public function getTicket($tid, $uid)
    {
        $query = "SELECT a.tid, a.uid, a.user_id, a.user_ip, a.subject, a.status, a.service_id, a.created_at, a.last_updated,
                            b.users_login, b.users_fname, b.users_lname, b.users_role, b.users_email
                    FROM OGP_DB_PREFIXtickets a
                        JOIN OGP_DB_PREFIXusers b
                            ON (a.user_id = b.user_id)
                    WHERE tid = $tid
                    AND uid = '".$this->db->real_escape_string($uid)."'";

        $result = $this->db->resultQuery($query);

        if (is_array($result)) {
            $ticketInfo = $result[0];
            $ticketInfo['messages'] = $this->ticketMessageArray(
                $this->getMessages($tid),
                $this->getAttachments($tid)
            );
            
            return $ticketInfo;
        } else {
            return false;
        }
    }

    private function getMessages($tid)
    {
        $query = "SELECT a.reply_id, a.ticket_id, a.user_id, a.user_ip, a.message, a.date, a.rating, a.is_admin,
                            b.user_id, b.users_login, b.users_role, b.users_fname, b.users_lname, b.users_email, b.users_parent
                        FROM OGP_DB_PREFIXticket_messages a
                            JOIN OGP_DB_PREFIXusers b
                                ON (a.user_id = b.user_id)
                        WHERE a.ticket_id = $tid
                        ORDER BY a.reply_id DESC";

        return $this->db->resultQuery($query) ?: array();
    }

    private function getAttachments($tid)
    {
        $query = "SELECT attachment_id, reply_id, original_name, unique_name
                    FROM OGP_DB_PREFIXticket_attachments
                    WHERE ticket_id = $tid
                    ORDER BY reply_id DESC";

        return $this->db->resultQuery($query) ?: array();
    }

    private function ticketMessageArray($messages, $attachments)
    {
        $keys = array_keys($messages);
        $end = end($keys);

        $count = count(array_filter($attachments, function($f) {
            return is_null($f['reply_id']);
        }));
        
        foreach ($messages as $i => $message) {
            foreach ($attachments as $k => $v) {

                if ($messages[$i]['reply_id'] == $v['reply_id']) {
                    $messages[$i]['attachments'][] = $v;
                }

                if (is_null($v['reply_id']) && (!isset($messages[$end]['attachments']) || count($messages[$end]['attachments']) < $count)) {
                    $messages[$end]['attachments'][] = $v;
                }
            }
        }

        return $messages;
    }

    public function open($user_id, $user_ip, $subject, $message, $service_id, $is_admin)
    {
        $parent_id = $user_id;
        if ($this->db->isSubUser($user_id)) {
            $result = $this->db->resultQuery("SELECT users_parent FROM OGP_DB_PREFIXusers WHERE user_id = ".(int)$user_id);
            $parent_id = (int)$result[0]['users_parent'];
        }

        $uid = bin2hex(openssl_random_pseudo_bytes(4));

        // $this->db->resultInsertId calls real_escape_string on all the values.
        $fields = array(
            'uid'           =>  $uid,
            'user_id'       =>  $user_id,
            'parent_id'     =>  $parent_id,
            'user_ip'       =>  inet_pton($user_ip),
            'subject'       =>  $subject,
            'service_id'    =>  ($service_id === 0 ? null : (int)$service_id),
            'status'        =>  1
        );

        $insertId = $this->db->resultInsertId('tickets', $fields);
        if ($insertId !== false) {
            $this->message($insertId, $user_id, $user_ip, $message, $is_admin, $uid);
            $this->updateTimestamp($insertId, $uid);

            return array('uid' => $uid, 'tid' => $insertId);
        }

        return false;
    }

    public function message($tid, $user_id, $user_ip, $message, $is_admin, $uid)
    {
        $fields = array(
            'ticket_id'     =>  $tid,
            'user_id'       =>  $user_id,
            'user_ip'       =>  inet_pton($user_ip),
            'message'       =>  $message,
            'is_admin'      =>  ($is_admin ? '1' : '0')
        );

        $insertId = $this->db->resultInsertId('ticket_messages', $fields);
        
        if ($insertId !== false) {
            $this->updateStatus($tid, $uid, ($is_admin ? 2 : 3));
            $this->updateTimestamp($tid, $uid);
        }

        return $insertId;
    }

    // 0 = closed
    // 1 = open
    // 2 = admin response
    // 3 = customer response
    public function updateStatus($tid, $uid, $status)
    {
        $status = (int)$status;
        return $this->db->query("UPDATE OGP_DB_PREFIXtickets SET status = $status WHERE tid = $tid AND uid = '$uid'");
    }

    public function updateTimestamp($tid, $uid)
    {
        return $this->db->query("UPDATE OGP_DB_PREFIXtickets SET last_updated = NOW() WHERE tid = $tid AND uid = '$uid'");
    }

    public function exists($tid, $uid)
    {
        $query = "SELECT COUNT(1) AS ticketCount FROM OGP_DB_PREFIXtickets
                    WHERE `tid` = $tid AND
                        `uid` = '".$this->db->real_escape_string($uid)."'";
                        
        $result = $this->db->resultQuery($query);
        return ($result[0]['ticketCount'] == 0 ? false : true);
    }

    public function authorized($user_id, $tid, $uid)
    {
        $query = "SELECT a.user_id as utid, a.parent_id, b.user_id, b.users_parent
                    FROM OGP_DB_PREFIXtickets a
                        JOIN OGP_DB_PREFIXusers b
                        ON (
                            a.user_id = b.user_id
                            OR a.user_id = b.users_parent
                            OR a.parent_id = b.user_id
                            OR a.parent_id = b.users_parent
                        )
                    WHERE a.tid = ".(int)$tid." AND a.uid = '".$this->db->real_escape_string($uid)."'
                        AND (
                            b.user_id = ".(int)$user_id ."
                            OR b.users_parent = ".(int)$user_id."
                        )";

        $result = $this->db->resultQuery($query);
        return $result[0] ?: false;
    }

    public function getServices($user_id, $is_admin)
    {
        if ($is_admin) {
            $homes = $this->db->getHomesFor('admin', $user_id);
        } else {
            $homes = $this->db->getHomesFor('user_and_group', $user_id);
        }

        $return = array(
            array('home_id' => 0, 'home_name' => '')
        );

        if (!$homes) {
            return $return;
        }

        foreach ($homes as $home) {
            $return[] = array('home_id' => $home['home_id'], 'home_name' => $home['home_name']);
        }

        return $return;
    }

    public function setRating($tid, $reply_id, $rating)
    {
        $query = "UPDATE OGP_DB_PREFIXticket_messages
                    SET rating = ".(int)$rating."
                    WHERE ticket_id = ".(int)$tid." AND reply_id = ".(int)$reply_id;

        return $this->db->query($query);
    }

    // Move this to the attachment class...?
    public function getAttachmentById($attachment_id, $tid)
    {
        $query = "SELECT original_name, unique_name FROM OGP_DB_PREFIXticket_attachments
                    WHERE attachment_id = ".(int)$attachment_id." AND ticket_id = ".(int)$tid;

        $result = $this->db->resultQuery($query);
        return $result[0] ?: false;
    }
}