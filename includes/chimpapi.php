<?php


class ChimpApi
{

  const URL_BASE = 'https://edapi.campaigner.com/v1/';
  const API_KEY = 'df399e01-e7c4-4eba-bb10-0c3514c96808';
  const PAGE_SIZE = '500';

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
    curl_setopt($curlSession, CURLOPT_TIMEOUT, 20);
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

  public function allcampaigns()
  {
    $data = [];
    $responsedata = [];
    $date = new DateTime('1 days ago');

    $params = array('PageSize' => '10', 'Since' => $date->format('m/d/Y'));
    $response = $this->request('GET', 'Campaigns/sent', $params);

    foreach ($response['Items'] as  $campaigns) {
      array_push($responsedata, array('camp_id' => $campaigns['CampaignID'], 'camp_title' => $campaigns['Name'], 'camp_date' => $campaigns['ScheduledDate']));
    }
    $data['data'] = $responsedata;
    return $data;
  }


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




  public function clickdetail($campaign_id, $total_record, $pagenumber)
  {



    $PageSize = self::PAGE_SIZE;
    $data = [];
    $data['id'] = $campaign_id;
    $membersData = [];
   
    $params = array('PageSize' => $PageSize, 'PageNumber' => $pagenumber);
    $response = $this->request('GET', 'Campaigns/' . $campaign_id . '/Clicks', $params);

    if ($response) {
      $total = $response['TotalRecords'];
        if ($total) {
          if ($total_record < $total) {
        if ($response['Items']) {
            
              if(($response['TotalPages']) > $pagenumber)
             $pagenumber = $response['PageNumber'] + 1;
            
             

            $data['metadata'] = array('total_open' => $response['TotalRecords']);

               foreach ($response['Items'] as $key => $members) {


                array_push($membersData, array(
                  'email' => $members["EmailAddress"],
                  'custom_field' => json_encode($members['CustomFields']),
                  'clicks' => 1,
                  'clicked_date' => $members['ActionDate']
                ));
              }
              
              $data['logs'] = array('camp_id' => $campaign_id, 'type' => 'clicked', 'count' => count($membersData), 'pagenumber' => $pagenumber);
               $data['data'] = $membersData;
            }

            
          }
        }
        
        
      }
     
    
    
    return $data;
  }

  public function opendetail($campaign_id, $total_record, $pagenumber)
  {
    $data = [];
    $data['id'] = $campaign_id;
    $membersData = [];
    $PageSize = self::PAGE_SIZE;
     $pagenumber = $pagenumber + 1;

    $params = array('PageSize' => $PageSize, 'PageNumber' => $pagenumber);
    $response = $this->request('GET', 'Campaigns/' . $campaign_id . '/Opens', $params);
    
    

    if ($response) {
      $total = $response['TotalRecords'];

      if ($total) {
           if ($total_record < $total) {
        if ($response['Items']) {
            
           if(($response['TotalPages']) > $pagenumber)
             $pagenumber = $response['PageNumber'] + 1;
             
               $data['metadata'] = array('total_click' => $response['TotalRecords']);
             
            foreach ($response['Items'] as $key => $members) {


              array_push($membersData, array(
                'email' => $members["EmailAddress"],
                'custom_field' => json_encode($members['CustomFields']),
                'opens' => 1,
                'opened_date' => $members['ActionDate']
              ));
            }
            
             $data['logs'] = array('camp_id' => $campaign_id, 'type' => 'opened', 'count' => count($membersData), 'pagenumber' => $pagenumber);
              $data['data'] = $membersData;
          }

         }
      }
     
    }
    
    return $data;
  }
}
