<?php
require_once('chimpapi.php');
require_once('dbconnect.php');
require_once('config.php');
class SaveChimpData
{

  public function __construct()
  {
    $this->api = new ChimpApi();
    $this->db =   mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABSE);
  }


  public function reset_cron()

  {
    mysqli_query($this->db, "UPDATE campaigns SET `cron_status`='0'");
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
      $this->add_campaignmeta($camp_id);
      $this->add_opendetail($camp_id);
      $this->add_clickdetail($camp_id);
    }
  }

  public function update_campaigns()

  {
    $sql = "SELECT camp_id FROM  campaigns where `cron_status`='0' ORDER BY id";

    if ($result = mysqli_query($this->db, $sql)) {

      while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
      }

      foreach ($rows as $row) {
        $this->add_campaignmeta($row['camp_id']);
        $this->add_opendetail($row['camp_id']);
        $this->add_clickdetail($row['camp_id']);
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

  public function updateOpenedmeta($campaign_id, $key, $count)
  {
    $sql = "SELECT meta_value FROM  campaigns_metadata  where camp_id='$campaign_id' and meta_key = '" . $key . "'  ORDER BY id";
    $result = mysqli_query($this->db, $sql);
    $row = $result->fetch_assoc();
    if (isset($row) && !empty($row)) {
      $total_record = $row['meta_value'] + $count;
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
        foreach ($response['data'] as $key => $members) {
          $email = $members['email'];
          $opened_date = date('Y-m-d', strtotime($members['opened_date']));
          $custom_field = $members['custom_field'];
          $opens = $members['opens'];
          $sql = "SELECT * FROM reports_open WHERE camp_id ='$campaign_id' and email = '$email' LIMIT 1";
          $result = mysqli_query($this->db, $sql);
          if (mysqli_num_rows($result) > 0) {
            $total = $total + 1;
          } else {
            $query = "insert into reports_open(camp_id,email,opens,opened_date,custom_field) values('$campaign_id','$email','$opens','$opened_date','$custom_field')";
            mysqli_query($this->db, $query);
          }
        }
        $this->updateOpenedmeta($campaign_id, 'unique_open', $total);
      }
    }
  }


  function add_clickdetail($campaign_id)
  {
    $response = $this->api->clickdetail($campaign_id);

    if ($response) {
      if ($response['data']) {
        $existrow = 0;
        foreach ($response['data'] as $key => $members) {

          $email = $members['email'];

          $clicked_date = date('Y-m-d', strtotime($members['clicked_date']));
          $clicks = $members['clicks'];
          $custom_field = $members['custom_field'];

          $sql = "SELECT * FROM reports_click WHERE camp_id ='$campaign_id' and email = '$email' LIMIT 1";
          $result = mysqli_query($this->db, $sql);

          if (mysqli_num_rows($result) > 0) {

            $existrow = $existrow + 1;
          } else {
            $query = "insert into reports_click(camp_id,email,clicks,clicked_date,custom_field) values('$campaign_id','$email','$clicks','$clicked_date','$custom_field')";
            mysqli_query($this->db, $query);
          }
        }
      }
    }
  }
}