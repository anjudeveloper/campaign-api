<!DOCTYPE html>
<html lang="en">
  <head>
    <?php
    session_start();
   require_once('includes/config.php'); 
    
    ?>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Campaigns reports</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="<?=APP_URL;?>/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?=APP_URL;?>/assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="<?=APP_URL;?>/assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="<?=APP_URL;?>/assets/vendors/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="<?=APP_URL;?>/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="<?=APP_URL;?>/assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="<?=APP_URL;?>/assets/images/favicon.png" />
   <link rel="stylesheet" href="<?=APP_URL;?>/assets/vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
  </head>
  <body>