<?php

  require_once('includes/campaignerdata.php'); 
 
  $SaveChimpData = new SaveChimpData();
 // $SaveChimpData->reset_cron();
  $SaveChimpData->add_campaigns();
  //$SaveChimpData->update_campaigns();
  

?>