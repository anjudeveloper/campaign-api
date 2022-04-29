<?php


class ChimpApi
{

  const URL_BASE = 'https://edapi.campaigner.com/v1/';
  const API_KEY = 'df399e01-e7c4-4eba-bb10-0c3514c96808';
  const PAGE_SIZE = '1000';

  /**
   *
   * @param string $method
   * @param string $target
   * @return array
   */
  public function request($method, $target, $params = null)
  {
    $curlSession = curl_init();
    $params['apikey'] = self::API_KEY;

    if ($method == 'GET')
      $target .= '?' . http_build_query($params);
    curl_setopt($curlSession, CURLOPT_URL, self::URL_BASE . $target);
    curl_setopt($curlSession, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'ApiKey: ' . self::API_KEY
    ));
    curl_setopt($curlSession, CURLOPT_USERAGENT, 'PHP-MCAPI/3.0');

    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlSession, CURLOPT_TIMEOUT, 30);
    curl_setopt($curlSession, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curlSession, CURLOPT_POST, true);
    curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, false);
    if ($params) {

      curl_setopt($curlSession, CURLOPT_POSTFIELDS, $params);
    }
    if (curl_exec($curlSession) === false) {
      echo 'Curl error: ' . curl_error($curlSession);
    }
    $response = curl_exec($curlSession);

    curl_close($curlSession);
    $responseArray = json_decode($response, true);

    return $responseArray;
  }


  /** function for get All camaigns campaigner */
  public function allcampaigns()
  {
    $data = [];
    $responsedata = [];
    $date = new DateTime('1 days ago');
    $params = array('PageSize' => '2', 'Since' =>$date->format('m/d/Y'));
   
    $response = $this->request('GET', 'Campaigns/sent', $params);
     print_r($response );
    foreach ($response['Items'] as  $campaigns) {
      array_push($responsedata, array('camp_id' => $campaigns['CampaignID'], 'camp_title' => $campaigns['Name'], 'camp_date' => $campaigns['ScheduledDate']));
    }
    $data['data'] = $responsedata;
    return $data;
  }
  /** function for get  camaign metadata campaigner */

  public function campaign($campaign_id)
  {
    $data = [];

    $response = $this->request('GET', 'Campaigns/' . $campaign_id);

    if ($response) {

      $data = array(
        'opens_total' => $response['Opens'],
        'open_rate' => $response['OpenRate'],
        'clicks_total' => $response['UniqueClicks'],
        'click_rate' => $response['UniqueRate'],
      );
    }

    return $data;
  }
  /** function for get Clicked data from campaigner */
  public function clickdetail($campaign_id, $pagenumber)
  {

    $PageSize = self::PAGE_SIZE;
    $data['id'] = $campaign_id;
    $data = $data['logs'] = $data['metadata'] = $membersData = [];
    $data_status = false;
    
    $params = array('PageSize' => $PageSize, 'PageNumber' => $pagenumber);
    
    $response = $this->request('GET', 'Campaigns/' . $campaign_id . '/Clicks', $params);
    if ($response) {
      $PageNumberre = $response['PageNumber'];
      $totalpagenumber =  $response['TotalPages'];
      if ($PageNumberre == $totalpagenumber) {
        $data_status = 1;
        $pagenumber = 1;
      } else {
        $pagenumber = $pagenumber + 1;
      }
      $total = $response['TotalRecords'];
      if ($total) {

        if ($response['Items']) {


          $data['metadata'] = array('total_open' => $response['TotalRecords']);

          foreach ($response['Items'] as $key => $members) {


            array_push($membersData, array(
              'email' => $members["EmailAddress"],
              'custom_field' => json_encode($members['CustomFields']),
              'clicks' => 1,
              'clicked_date' => $members['ActionDate']
            ));
          }
          $data['logs'] = array('camp_id' => $campaign_id,'total_record'=>$total, 'data_status' => $data_status, 'type' => 'clicked', 'count' => count($membersData), 'pagenumber' => $pagenumber, 'totalpagenumber' => $totalpagenumber);

          $data['data'] = $membersData;
        }
      }
    }




    return $data;
  }
  /** function for get Clicked date form campaigner */
  public function opendetail($campaign_id, $pagenumber)
  {
    $PageSize = self::PAGE_SIZE;
    $data['id'] = $campaign_id;
    $data = $data['logs'] = $data['metadata'] = $membersData = [];
    $data_status = false;
   
    $params = array('PageSize' => $PageSize, 'PageNumber' => $pagenumber);
    $response = $this->request('GET', 'Campaigns/' . $campaign_id . '/Opens', $params);
    if ($response) {
      $pagenumber = $response['PageNumber'];
      $totalpagenumber =  $response['TotalPages'];
      if ($pagenumber == $response['TotalPages']) {
        $data_status = 1;
        $pagenumber = 1;
      } else {
        $pagenumber = $pagenumber + 1;
      }
      $total = $response['TotalRecords'];
      if ($total) {

        if ($response['Items']) {

          $data['metadata'] = array('total_click' => $response['TotalRecords']);

          foreach ($response['Items'] as $key => $members) {


            array_push($membersData, array(
              'email' => $members["EmailAddress"],
              'custom_field' => json_encode($members['CustomFields']),
              'opens' => 1,
              'opened_date' => $members['ActionDate']
            ));
          }
          $data['logs'] = array('camp_id' => $campaign_id,'total_record'=>$total, 'data_status' => $data_status, 'type' => 'opened', 'count' => count($membersData), 'pagenumber' => $pagenumber, 'totalpagenumber' => $totalpagenumber);
          $data['data'] = $membersData;
        }
      }
    }

    return $data;
  }
}
