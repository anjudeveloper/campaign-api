<?php

require_once('config.php');

class GetChimpData
{

    public function __construct()
    {

        $this->db =   mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABSE);
    }

    function get_allcampaigns()
    {
        $results_array = array();
        $sql = 'SELECT campaigns.camp_id,campaigns.camp_title,campaigns.camp_date FROM  campaigns where cron_status=1 ORDER BY id DESC';
        $result = mysqli_query($this->db, $sql);
        $campdata = '';
        $results_array = [];
        while ($row = $result->fetch_assoc()) {

            $camp_meta = $this->get_campaignmeta($row['camp_id']);
            
            array_push($results_array, array(
                'camp_id' => $row['camp_id'],
                'camp_title' => $row['camp_title'],
                'open_rate' => $camp_meta['open_rate'],
                'click_rate' => $camp_meta['click_rate'],
                'total_record_open' => $camp_meta['total_record_open'],
                'total_record_clicked' => $camp_meta['total_record_clicked'],
                'opens_total' => isset($camp_meta['unique_open']) ? $camp_meta['unique_open'] : $camp_meta['opens_total'],
                'clicks_total' => isset($camp_meta['unique_click']) ? $camp_meta['unique_click'] : $camp_meta['clicks_total'],
                'camp_date' => $row['camp_date'],
            ));
        }
        return $results_array;
    }

    function get_campaignmeta($camp_id, $meta_type = null)
    {
        $data = [];
        $where = '';
        $results_array = array();
        if ($meta_type)
            $where = " and meta_key = '$meta_type'";

        $sql = "SELECT * FROM  campaigns_metadata  where camp_id='$camp_id' $where ORDER BY id";
        $result = mysqli_query($this->db, $sql);

        while ($row = $result->fetch_assoc()) {
            $results_array[] = $row;
        }

        foreach ($results_array as $key => $row) :
            $data[$row['meta_key']] = $row['meta_value'];
        endforeach;

        return $data;
    }

    function get_clickeddetail($camp_id)
    {
        $results_array = array();

        $sql = "SELECT * FROM  reports_click  where camp_id='$camp_id' ORDER BY id";
        $result = mysqli_query($this->db, $sql);
        while ($row = $result->fetch_assoc()) {
            $results_array[] = $row;
        }
        return $results_array;
    }

    function get_openendetail($camp_id)
    {
        $results_array = array();
        $sql = "SELECT * FROM  reports_open where camp_id='$camp_id' ORDER BY id";
        $result = mysqli_query($this->db, $sql);
        while ($row = $result->fetch_assoc()) {
            $results_array[] = $row;
        }
        return $results_array;
    }

    function get_customdata($camp_id = '')
    {
        $results_arrays = array();
        $sql = "SELECT * FROM  campaigns_metadata where meta_key='custom_field' ORDER BY camp_id";
        $results = mysqli_query($this->db, $sql);
        while ($rows = $results->fetch_assoc()) {
            $results_arrays[] = $rows;
        }
        return $results_arrays;
    }
    function getcustomfield($campid, $table)
    {

        $sql = "SELECT custom_field FROM $table WHERE camp_id = '$campid' and custom_field  !='NULL' limit 1";
        $results = mysqli_query($this->db, $sql);

        while ($rows = $results->fetch_assoc()) {
            $results_arrays = $rows;
        }
        return $results_arrays;
    }
}