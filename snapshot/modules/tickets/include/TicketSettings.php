<?php

class TicketSettings
{
    private $db;

    public function __construct(OGPDatabase $db)
    {
        $this->db = $db;
    }

    public function get($setting = '*')
    {
        $query = "SELECT setting_name, setting_value FROM OGP_DB_PREFIXticket_settings";
        
        if (is_array($setting) && !empty($setting)) {
            $in = '';
            $query .= ' WHERE setting_name IN (';

            foreach ($setting as $setting_name) {
                $in .= "'". $setting_name ."', ";
            }

            $query .= rtrim($in, ', ');
            $query .= ')';
        } elseif (!empty($setting) && $setting !== '*') {
            $query = $query . " WHERE setting_name = '".$setting."'";
        }

        $result = $this->db->resultQuery($query);
        return $result ? $this->flatten($result) : false;
    }

    public function set($settings)
    {
        foreach ($settings as $setting_name => $setting_value) {
            $query = $this->buildQueryString($setting_name, $setting_value);
            $this->db->query($query);
        }
    }

    private function buildQueryString($setting_name, $setting_value)
    {
        $setting_name = $this->db->real_escape_string($setting_name);
        $setting_value = $this->db->real_escape_string($setting_value);

        $queryString = "INSERT INTO OGP_DB_PREFIXticket_settings (setting_name, setting_value)
                            VALUES (
                                '". $setting_name ."', '". $setting_value ."'
                            )

                            ON DUPLICATE KEY UPDATE setting_value = '". $setting_value ."'";

        return $queryString;
    }

    private function flatten($arr)
    {
        $newArr = array();

        foreach ($arr as $k) {
            $newArr[$k['setting_name']] = $k['setting_value'];
        }

        return $newArr;
    }
}