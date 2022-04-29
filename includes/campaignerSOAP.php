<?php

class SoapRequest
{

    const SOAP_URL = 'https://ws.campaigner.com/2013/01/';
    const SOAP_USER = 'wiz+upapi@dedicatedemails.com';
    const SOAP_PASSWORD = 'TydLD2lA^2';
    function curelRequest($xml_post_string,$SOAPAction,$method)
    {
        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction:".self::SOAP_URL .$SOAPAction,
            "Content-length: " . strlen($xml_post_string),
        ); //SOAPAction: your op URL

        $url = self::SOAP_URL . $method.'.asmx';

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, self::SOAP_USER . ":" . self::SOAP_PASSWORD); // username and password - declared at the top of the doc
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch);

        curl_close($ch);

        // converting
        $response1 = str_replace("<soap:Body>", "", $response);
        $response2 = str_replace("</soap:Body>", "", $response1);
         $parser = simplexml_load_string($response2);
         return $parser;
        
    }
    
    function ListCampaigns()
    {
        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="https://ws.campaigner.com/2013/01">
   <soapenv:Header/>
   <soapenv:Body>
     <ListCampaigns xmlns="https://ws.campaigner.com/2013/01">
         <!--Optional:-->
         <ns:authentication>
            <!--Optional:-->
            <ns:Username>' . self::SOAP_USER . '</ns:Username>
            <!--Optional:-->
            <ns:Password>' . self::SOAP_PASSWORD . '</ns:Password>
         </ns:authentication>
         <!--Optional:-->
         <dateTimeFilter>
   <FromDate>' .date('Y-m-d', strtotime(date('Y-m-d') .' -3 day')). '</FromDate>
     <ToDate>' .  date('Y-m-d')  . '</ToDate>
         </dateTimeFilter>
        <campaignStatus>Sent</campaignStatus>
     </ListCampaigns>
   </soapenv:Body>
</soapenv:Envelope>
';   // data from the form, e.g. some ID number
  
         $parser = $this->curelRequest($xml_post_string,'ListCampaigns','campaignmanagement');      
         return $parser->ListCampaignsResponse->ListCampaignsResult->CampaignDescription;
    }
	
	function GetCampaignRunsSummaryReport($campID)
	{
		 $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="https://ws.campaigner.com/2013/01">
   <soapenv:Header/>
   <soapenv:Body>
     <GetCampaignRunsSummaryReport xmlns="https://ws.campaigner.com/2013/01">
         <!--Optional:-->
         <ns:authentication>
            <!--Optional:-->
            <ns:Username>' . self::SOAP_USER . '</ns:Username>
            <!--Optional:-->
            <ns:Password>' . self::SOAP_PASSWORD . '</ns:Password>
         </ns:authentication>
         <!--Optional:-->
        <campaignFilter>
        <CampaignIds><int>'.$campID.'</int></CampaignIds>
        </campaignFilter>
  
     </GetCampaignRunsSummaryReport>
   </soapenv:Body>
</soapenv:Envelope>
';   // data from the form, e.g. some ID number


         $parser = $this->curelRequest($xml_post_string,'GetCampaignRunsSummaryReport','campaignmanagement'); 
         
		return $parser->GetCampaignRunsSummaryReportResponse->GetCampaignRunsSummaryReportResult->Campaign;
	}
    function SoapReportTicket($campID,$action)
    {

        // Web Service contactmanagement.asmx
        // Method  RunReport

        // xml post structure

        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="https://ws.campaigner.com/2013/01">
   <soapenv:Header/>
   <soapenv:Body>
      <ns:RunReport>
         <!--Optional:-->
         <ns:authentication>
            <!--Optional:-->
            <ns:Username>' . self::SOAP_USER . '</ns:Username>
            <!--Optional:-->
            <ns:Password>' . self::SOAP_PASSWORD . '</ns:Password>
         </ns:authentication>
         <!--Optional:-->
         <ns:xmlContactQuery><![CDATA[<contactssearchcriteria><version major="3" minor="0" build="0" revision="0" />
         <accountid>1325099</accountid><set>Partial</set><evaluatedefault>True</evaluatedefault><testContactsOnly>False</testContactsOnly><group>
         <filter><filtertype>EmailAction</filtertype><campaign><campaignid>'.$campID.'</campaignid></campaign><action><status>Do</status><operator>'.$action.'</operator>
         </action><operator>Anytime</operator></filter></group></contactssearchcriteria>]]></ns:xmlContactQuery>
      </ns:RunReport>
   </soapenv:Body>
</soapenv:Envelope>
';   // data from the form, e.g. some ID number

        $parser = $this->curelRequest($xml_post_string,'RunReport','contactmanagement'); 
        $ticket_id = $parser->RunReportResponse->RunReportResult;
        return $ticket_id;
    }
    
    
     function SoapDownloadReport($ticketId,$from,$to)
    {

        
       
    
        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="https://ws.campaigner.com/2013/01">
   <soapenv:Header/>
   <soapenv:Body>
      <ns:DownloadReport>
          <ns:authentication>
            <!--Optional:-->
            <ns:Username>' . self::SOAP_USER . '</ns:Username>
            <!--Optional:-->
            <ns:Password>' . self::SOAP_PASSWORD . '</ns:Password>
         </ns:authentication>
         <ns:reportTicketId>'.$ticketId.'</ns:reportTicketId>
         <ns:fromRow>'.$from.'</ns:fromRow>
         <ns:toRow>'.$to.'</ns:toRow>
         <ns:reportType>rpt_Summary_Contact_Results_by_Campaign</ns:reportType>
      </ns:DownloadReport>
   </soapenv:Body>
</soapenv:Envelope>

';   // data from the form, e.g. some ID number

      
           $parser = $this->curelRequest($xml_post_string,'DownloadReport','contactmanagement'); 
        
        return $parser->DownloadReportResponse->DownloadReportResult->ReportResult;
    }
    
    public function allcampaigns()
  {
$ListCampaigns = $this->ListCampaigns();
$data = [];
    $responsedata = [];
foreach($ListCampaigns as $ListCampaign)
{
   $campID = $ListCampaign; 
  $SoapReportTicket = $this->GetCampaignRunsSummaryReport($campID->Id);
  $CampaignRun = $SoapReportTicket->CampaignRuns->CampaignRun;
  $name = $SoapReportTicket->Name;
  $Opens = $CampaignRun->Domains->Domain->ActivityResults->Opens;
  $Clicks = $CampaignRun->Domains->Domain->ActivityResults->Clicks;
  $ScheduledDate = $CampaignRun->ScheduledDate;
   array_push($responsedata, array('camp_id' => $campID->Id, 'camp_title' => $name, 'camp_date' => $ScheduledDate));
  //date('d-m-Y', strtotime($ScheduledDate))
 } 

  $data['data'] = $responsedata;
    return $data;
}

public function opendetail($campId)
{
    $recrods = [];
    $membersData = [];
$SoapReportTicket = $this->SoapReportTicket($campId,'Open');  
print_r($SoapReportTicket);
$from = 1;
        $to = $SoapReportTicket->RowCount[0];
        if($SoapReportTicket->RowCount[0] > 25000)
        {
         $i = 1;$Emails = [];
         $opendetails1 = $this->SoapDownloadReport($SoapReportTicket->ReportTicketId[0],$from,25000);
   foreach($opendetails1 as $opendetail){
       $data = [];
foreach($opendetail->Contact->attributes() as $a => $b)
  {if($a=='Email')  { 
      $email = (string)$b;
      if (!in_array($email, $Emails)) {
      $data['email'] = $email;
      $Emails[] =$email ; $i++;}
      
       }
         if($data) {
           if($a=='DateCreatedUTC'){
               $data['date'] = (string)$b;
           
       }
           
       }
      
  }
       
       
   if($data) { array_push($membersData, array(
              'email' => $data["email"],
              'custom_field' => 'fileds',
              'opens' => 1,
              'opened_date' => $data['date']
            ));}
       
   }
   $opendetails2 = $this->SoapDownloadReport($SoapReportTicket->ReportTicketId[0],25000,$to); 
   
   
        
   foreach($opendetails2 as $opendetail){$data = [];
foreach($opendetail->Contact->attributes() as $a => $b)

  {if($a=='Email')  { 
      $email = (string)$b; 
      if (!in_array($email, $Emails)) 
      {
      $data['email'] = $email;
      $Emails[] =$email ; $i++;
      
  }
      
  }
      
       if($data) {
           if($a=='DateCreatedUTC'){$data['date'] = (string)$b;
           
       }
           
       }
      
  }
       
       
   if($data) { 
       array_push($membersData, array(
              'email' => $data["email"],
              'custom_field' => 'fileds',
              'opens' => 1,
              'opened_date' => $data['date']
            ));}
       
   }
            
 
     
        } else  {
             $i = 1;$Emails = [];
            
          $opendetails1 = $this->SoapDownloadReport($SoapReportTicket->ReportTicketId[0],$from,$to);
   foreach($opendetails1 as $opendetail){
       $data = [];
foreach($opendetail->Contact->attributes() as $a => $b)
  {if($a=='Email')  { 
      $email = (string)$b;
      if (!in_array($email, $Emails)) {
      $data['email'] = $email;
      $Emails[] =$email ; $i++;}
      
       }
         if($data) {
           if($a=='DateCreatedUTC'){
               $data['date'] = (string)$b;
           
       }
           
       }
      
  }
       
       
   if($data) { array_push($membersData, array(
              'email' => $data["email"],
              'custom_field' => 'fileds',
              'opens' => 1,
              'opened_date' => $data['date']
            ));}
       
   }   
        }

  $recrods['data'] = $membersData;
  
  return $recrods;

}

public function clickdetail($campId)
{
    $recrods = [];
    $membersData = [];
$SoapReportTicket = $this->SoapReportTicket($campId,'ClickAnyLink');  

          $from = 1;
        $to = $SoapReportTicket->RowCount[0];
        
         $i = 1;$Emails = [];
         
         
         $opendetails1 = $this->SoapDownloadReport($SoapReportTicket->ReportTicketId[0],$from,$to);
   
   foreach($opendetails1 as $opendetail){
       $data = [];
foreach($opendetail->Contact->attributes() as $a => $b)
  {if($a=='Email')  { 
      $email = (string)$b;
      if (!in_array($email, $Emails)) {
      $data['email'] = $email;
      $Emails[] =$email ; $i++;}
      
       }
         if($data) {
           if($a=='DateCreatedUTC'){
               $data['date'] = (string)$b;
           
       }
           
       }
      
  }
       
       
   if($data) { array_push($membersData, array(
              'email' => $data["email"],
              'custom_field' => 'fileds',
              'clicks' => 1,
              'clicked_date' => $data['date']
            ));}
       
   }
  $recrods['data'] = $membersData;
  
  return $recrods;

}

}
 
?>