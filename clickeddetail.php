<?php include_once('partials/header.php'); 
require_once('includes/getrecord.php'); 
$getChimpData = new GetChimpData();
if($_GET['camp_id'])
{
  $_SESSION['camp_id'] =$_GET['camp_id'];
  $camp_id = $_GET['camp_id'];
}
else
{
$camp_id = $_SESSION['camp_id'];
}

  $rescampaign = $getChimpData->get_campaignmeta($camp_id);
  $CFheading = $getChimpData->getcustomfield($camp_id,'reports_click');
$custom_field = json_decode($CFheading['custom_field']);
?>
<div class="container-scroller">
      <!-- partial:partials/_navbar.html -->
      <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
       <div class="container">
       <div class="btn-group bg-white p-3" role="group" aria-label="Basic example">
                  <a href="<?=APP_URL;?>/index.php" class="btn btn-link text-light py-0 border-right">Dashboard</a>
                  <?php if($_SESSION['camp_id']) { ?>
                  <a href="<?=APP_URL;?>/openeddetail.php" class="btn btn-link text-light py-0 border-right">Opened</a>
                  <a href="<?=APP_URL;?>/clickeddetail.php" class="btn btn-link text-dark py-0">Clicked</a>
                  <?php } ?>
                </div>
                </div>
      </nav>
<!-- partial -->
      <div class="container page-body-wrapper">
       
         <div class="content-wrapper">
         
           
                <div class="row">
                   
                    <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Clicked Reports</h4>
                  
                     <p class="card-description"> 
                         Total Click <code><?=$rescampaign['unique_click'];?></code>
                        
                    </p>
                    <table class="table" id="recorddataTable">
                      <thead>
                        <tr>
                          <th>Sr NO.</th>
                          <th>Email address.</th>
                          <?php
                          if($custom_field)
                          {
                          foreach($custom_field as $key=>$value)
                          {
                          echo '<th>'.$key.'</th>';
                          }
                          }
                          ?>
                          <th>Click Count</th>
                          <th>Clicked Date</th>
                         
                        </tr>
                      </thead>
                      <tbody>
                          
                          
                      </tbody>
                    </table>
                   
                  </div>
                </div>
              </div>
                
              </div>
            </div>
           
            </div>
           
<?php include_once('partials/footer.php'); ?>
<script>
           $(document).ready(function(){
   $('#recorddataTable').DataTable({
        "scrollY":        "600px",
        "pageLength": 50,
        "lengthChange": false,
        "scrollCollapse": true,
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',
      'ajax': {
          'url':'<?=APP_URL;?>/includes/ajax-data.php',
            data: {
    "camp_id": "<?=$camp_id?>",//send value to server as $_POST['name1']
    "length":"50",
    "recrodtype":"clicked"
 }
      },
      'columns': [
          { data: 'sr_no' },
         { data: 'email' },
          <?php
                          if($custom_field)
                          {
                          foreach($custom_field as $key=>$value)
                          {
                          echo "{ data: '".$key."' },";
                          }
                          }
                          ?>
         { data: 'status' },
         { data: 'date' },
       
      ]
   });
});
</script>