<?php
// Turn off error reporting

include_once('partials/header.php'); 
require_once('includes/getrecord.php'); 
$getChimpData = new GetChimpData();
$get_allcampaigns = $getChimpData->get_allcampaigns();

?>
<style>.container{max-width:100%}</style>
<div class="container-scroller">
      <!-- partial:partials/_navbar.html -->
      <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
       <div class="container">
       <div class="btn-group bg-white p-3" role="group" aria-label="Basic example">
                  <a href="<?=APP_URL;?>/index.php" class="btn btn-link text-dark py-0 border-right">Dashboard</a>
                  <?php if($_SESSION['camp_id']) { ?>
                  <a href="<?=APP_URL;?>/openeddetail.php" class="btn btn-link text-light py-0 border-right">Opened</a>
                  <a href="<?=APP_URL;?>/clickeddetail.php" class="btn btn-link text-light py-0">Clicked</a>
                  <?php } ?>
                </div>
                </div>
      </nav>
      <!-- partial -->
      <div class="container page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        
        <!-- partial -->
       
          <div class="content-wrapper">
           
            <div class="d-xl-flex justify-content-between align-items-start">
              <h2 class="text-dark font-weight-bold mb-2"> Reports   </code></h2>
            
            </div>
            <div class="row">
                 <div class="col-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                  <table class="table" id="campaignsList">
                      <thead>
                        <tr>
                          <th style="width:30px">Sr NO.</th>
                          <th>Campaigns name</th>
                         
                        <th>Total opened</th>
                        <th>Total clicked</th>
                        
                         
                          <th>Date Scheduled Date</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                          
                          <?php
                           
                           
                           foreach($get_allcampaigns as $key=>$campaign)
                          {
                              ?>
                        <tr>
                              <td style="width:30px"><?=$key+1;?></td>
                          <td><?=$campaign['camp_title'];?></td>
                          <td><?=$campaign['opens_total'];?></td>
                          <td><?=$campaign['clicks_total'];?></td>
                         
                          <td><?=$campaign['camp_date'];?> </td>
                          <td><a class="btn btn-primary" href="openeddetail.php/?camp_id=<?=$campaign['camp_id']?>">Opened</a>
                          <a class="btn btn-primary" href="clickeddetail.php/?camp_id=<?=$campaign['camp_id']?>">Clicked</a>
                        </td>
                          
                        </tr>
                        <?php } 
                       
                        ?>
                      </tbody>
                    </table>
                   
                  </div>
                </div>
              </div>
             
            </div>
          </div>
            </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
          <!--footer class="footer">
            <div class="footer-inner-wraper">
              <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2020 . All rights reserved.</span>
                <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="mdi mdi-heart text-danger"></i></span>
              </div>
            </div>
          </footer-->
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
  
<?php include_once('partials/footer.php'); ?>
<script>
      
      jQuery(document).ready(function($) {
              if($('#campaignsList').length) {
    $('#campaignsList').DataTable({
        "columnDefs": [
    { "width": "20%", "targets": 0 }
        ],
      "pageLength":20
    });
              }
} );
  </script>