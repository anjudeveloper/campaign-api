<?php
require_once('campaignerSOAP.php');
require_once('config.php');

class SaveChimpData
{

  public function __construct()
  {
    $this->api = new SoapRequest();
    $this->db =   mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABSE);
  }


  public function reset_cron()

  {
    mysqli_query($this->db, "UPDATE campaigns SET `cron_status`='0'");
   // mysqli_query($this->db, "UPDATE logs SET `data_status`='0' ,`pagenumber` = '0'");
  }



  public  function add_campaigns()
  {

 $listCamps = $this->api->allcampaigns();
 
   foreach ($listCamps['data'] as $listCamp) {
      $camp_id = $listCamp["camp_id"];
      $camp_title = $listCamp["camp_title"];
      $camp_date = $listCamp["camp_date"];
      $sql = "SELECT * FROM  campaigns  where camp_id='$camp_id' ORDER BY id";
      $result = mysqli_query($this->db, $sql);
      $row = $result->fetch_assoc();
      if (isset($row) && !empty($row)) {

        mysqli_query($this->db, "UPDATE campaigns SET `cron_status`='1' where camp_id ='$camp_id'");
      } else {
        $query = "insert into campaigns(camp_id,camp_title,camp_date,cron_status) values('$camp_id','$camp_title','$camp_date','1')";
        $ret = mysqli_query($this->db, $query);
      }
    //  $this->add_campaignmeta($camp_id);
      $this->add_opendetail($camp_id);
    $this->add_clickdetail($camp_id);
     /* $sql = "SELECT pagenumber,data_status,totalpgnumber FROM  logs where camp_id ='" . $camp_id . "' and event_type ='opened' ORDER BY id desc limit 1";
      $result = mysqli_query($this->db, $sql);
      $row = $result->fetch_assoc();
      if (isset($row) && !empty($row)) {
        $totalrow = $row['totalpgnumber'];
        $pagenumber = $row['pagenumber'];
        for ($x = 0; $x <= $totalrow; $x++) {
          $pagenumber = $pagenumber + 1;
          if ($row['data_status'] == 1) return;
          echo $x . 'open' . $camp_id . '</br>';
          $this->add_opendetail($camp_id, $pagenumber);
        }
      } else {
        $this->add_opendetail($camp_id, 1);
      }

      $sql = "SELECT pagenumber,data_status,totalpgnumber FROM  logs where camp_id ='" . $camp_id . "' and event_type ='clicked' ORDER BY id desc limit 1";
      $result = mysqli_query($this->db, $sql);
      $row = $result->fetch_assoc();
      if (isset($row) && !empty($row)) {
        $totalrow = $row['totalpgnumber'];
        $pagenumber = $row['pagenumber'];
        for ($x = 0; $x <= $totalrow; $x++) {
          $pagenumber = $pagenumber + 1;
          echo $x . 'click' . $camp_id . '</br>';
          if ($row['data_status'] == 1) return;
          $this->add_clickdetail($camp_id, $pagenumber);
        }
      } else {
        $this->add_clickdetail($camp_id, 1);
      }*/
    }
  }

  public function update_campaigns()

  {
    $rows = [];
    $sql = "SELECT camp_id FROM  campaigns where `cron_status`='0' ORDER BY id";

    if ($result = mysqli_query($this->db, $sql)) {

      while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
      }
      if ($rows) {
        foreach ($rows as $row) {

          $this->add_campaignmeta($row['camp_id']);
          $this->add_opendetail($row['camp_id']);
          $this->add_clickdetail($row['camp_id']);
        }
      }
    }
  }
  public function add_campaignmeta($campaign_id)
  {
    $campaigndata = $this->api->campaign($campaign_id);

    $sql = "SELECT * FROM  campaigns_metadata  where camp_id='$campaign_id'  ORDER BY id";
    $result = mysqli_query($this->db, $sql);
    $row = $result->fetch_assoc();
    if (isset($row) && !empty($row)) {
      foreach ($campaigndata as $key => $value) :
        mysqli_query($this->db, "UPDATE campaigns_metadata SET `meta_value`='$value' where camp_id ='" . $campaign_id . "' and meta_key ='" . $key . "' ");
      endforeach;
    } else {
      foreach ($campaigndata as $key => $value) :
        $que = "insert into campaigns_metadata(camp_id,meta_key,meta_value) values('$campaign_id','$key','$value')";
        mysqli_query($this->db, $que);

      endforeach;
    }
  }

  public function updateOpenedmeta($campaign_id, $key, $count,$update=true)
  {
    $sql = "SELECT meta_value FROM  campaigns_metadata  where camp_id='$campaign_id' and meta_key = '" . $key . "'  ORDER BY id";
    $result = mysqli_query($this->db, $sql);
    $row = $result->fetch_assoc();
    if (isset($row) && !empty($row)) {
      $total_record = $count;
      if($update)
      $total_record = $row['meta_value'];
      mysqli_query($this->db, "UPDATE campaigns_metadata SET `meta_value`='$total_record' where camp_id ='" . $campaign_id . "' and meta_key ='" . $key . "' ");
    } else {
      $que = "insert into campaigns_metadata(camp_id,meta_key,meta_value) values('$campaign_id','$key','$count')";
      mysqli_query($this->db, $que);
    }
  }


  public function add_opendetail($campaign_id)
  {

    $response = $this->api->opendetail($campaign_id);
    if ($response) {
      if ($response['data']) {
        $total = 0;
        $existrow = 0;
        $last_id = '';
        $last_recrod_date = '';
         $querydelete = "DELETE FROM reports_open WHERE camp_id = $campaign_id";
         mysqli_query($this->db, $querydelete);
        foreach ($response['data'] as $key => $members) {
          $email = $members['email'];
          $opened_date = $members['opened_date'];
          $custom_field = $members['custom_field'];
          $opens = $members['opens'];
         // $sql = "SELECT * FROM reports_open WHERE camp_id ='$campaign_id' and email = '$email' LIMIT 1";
          //$result = mysqli_query($this->db, $sql);
         
            $query = "insert into reports_open(camp_id,email,opens,opened_date,custom_field) values('$campaign_id','$email','$opens','$opened_date','$custom_field')";
            mysqli_query($this->db, $query);
            $last_id = $email;
            $total = $total + 1;
          
          $last_recrod_date = $opened_date;
        }
        $this->updateOpenedmeta($campaign_id, 'unique_open', $total,true);
       
        if (isset($response['logs'])) {
          $logs = $response['logs'];
         
          $this->updateOpenedmeta($campaign_id, 'total_record_open', $logs['total_record'],false);
          $logper = array(
            'campaign_id' => $campaign_id,
            'type' => $logs['type'],
            'pagenumber' => $logs['pagenumber'],
            'last_id' => $last_id,
            'last_recrod_date' => $last_recrod_date,
            'count' =>  $total,
            'data_status' => $logs['data_status'],
            'totalpagenumber' => $logs['totalpagenumber']
          );

          $this->logs($logper);
        }
      }
    }
  }


  function add_clickdetail($campaign_id)
  {

    $response = $this->api->clickdetail($campaign_id);

    if ($response) {
      if ($response['data']) {
        $existrow = 0;
        $total = 0;
        $last_id = '';
        $last_recrod_date = '';
        $querydelete = "DELETE FROM reports_click WHERE camp_id = $campaign_id";
         mysqli_query($this->db, $querydelete);
        foreach ($response['data'] as $key => $members) {

          $email = $members['email'];

          $clicked_date = $members['clicked_date'];
          $clicks = $members['clicks'];
          $custom_field = $members['custom_field'];

          $query = "insert into reports_click(camp_id,email,clicks,clicked_date,custom_field) values('$campaign_id','$email','$clicks','$clicked_date','$custom_field')";
            mysqli_query($this->db, $query);
            $last_id = $email;

            $total = $total + 1;
          
          $last_recrod_date = $clicked_date;
        }
        $this->updateOpenedmeta($campaign_id, 'unique_click', $total,true);
         
        if (isset($response['logs'])) {
          $logs = $response['logs'];
          
        $this->updateOpenedmeta($campaign_id, 'total_record_clicked', $logs['total_record'],false);
          $logper = array(
            'campaign_id' => $campaign_id,
            'type' => $logs['type'],
            'pagenumber' => $logs['pagenumber'],
            'last_id' => $last_id,
            'last_recrod_date' => $last_recrod_date,
            'count' => $total,
            'data_status' => $logs['data_status'],
            'totalpagenumber' => $logs['totalpagenumber']
          );

          $this->logs($logper);
        }
      }
    }
  }

  public function logs($logper)
  {
    $where = '';
    $campaign_id = $logper['campaign_id'];
    $type = $logper['type'];
    $last_id = $logper['last_id'];
    $count = $logper['count'];
    $pagenumber = $logper['pagenumber'];
    $last_recrod_date = $logper['last_recrod_date'];
    $totalpagenumber = $logper['totalpagenumber'];
    $data_status = $logper['data_status'];
    $sql = "SELECT * FROM  logs where camp_id ='" . $campaign_id . "' and event_type ='" . $type . "' $where ORDER BY id desc limit 1";
    $result = mysqli_query($this->db, $sql);
    $row = $result->fetch_assoc();
    if (isset($row) && !empty($row)) {
      $offset = $row['total_record'];
      $total_record = $offset + $count;

      mysqli_query($this->db, "UPDATE logs SET `total_record`='$total_record', `pagenumber` = '$pagenumber' ,`last_id`='$last_id',`totalpgnumber` = '$totalpagenumber' ,`data_status` = '$data_status' where camp_id ='" . $campaign_id . "' and event_type ='" . $type . "' ");
    } else {
      $total_record =  $count;
      $query = "insert into logs(camp_id,event_type,last_id,total_record,pagenumber,totalpgnumber,data_status) values('$campaign_id','$type','$last_id','$total_record','$pagenumber','$totalpagenumber','$data_status')";
      mysqli_query($this->db, $query);
    }
  }
}
