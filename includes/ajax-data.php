<?php

require_once('config.php');

$db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABSE);

## Read value
$custom_fild_heading = '';
$draw = $_POST['draw'];
$recrodtype = $_POST['recrodtype'];
$row = $_POST['start'];
$camp_id = $_POST['camp_id'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = 'id'; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value
$table = '';
if ($recrodtype == 'opened')
  $table = 'reports_open';
if ($recrodtype == 'clicked')
  $table = 'reports_click';
## Search 
$searchQuery = " ";
if ($searchValue != '') {
  $searchQuery = " and (email like '%" . $searchValue . "%') ";
}

## Total number of records without filtering
$sel = mysqli_query($db, "select count(*) as allcount from $table  where camp_id=" . $camp_id);
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];


## Total number of record with filtering
$sel = mysqli_query($db, "select count(*) as allcount from $table WHERE 1 and camp_id=" . $camp_id . $searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from $table WHERE 1 and camp_id=" . $camp_id . $searchQuery . " order by " . $columnName . " " . $columnSortOrder . " limit " . $row . "," . $rowperpage;
$empRecords = mysqli_query($db, $empQuery);
$data = array();
$sql = "SELECT custom_field FROM $table WHERE camp_id = '$camp_id' and custom_field  !='NULL' limit 1";
$results = mysqli_query($db, $sql);
$row = $results->fetch_assoc();
$custom_fild_heading = json_decode($row['custom_field']);

$start = $_POST['start'] + 1;
while ($row = mysqli_fetch_assoc($empRecords)) {
  $datafield = [];
  if ($custom_fild_heading) {
    if (json_decode($row['custom_field'])) {
      $chstom_filed = json_decode($row['custom_field']);
    } else {
      $chstom_filed = 'null';
    }
  }
  if ($recrodtype == 'opened') {
    $datafield = array(
      "sr_no" => $start++,
      "email" => $row['email'],
      "status" => $row['opens'],
      "date" => $row['opened_date'],
    );
  }

  if ($recrodtype == 'clicked') {
    $datafield = array(
      "sr_no" => $start++,
      "email" => $row['email'],
      "status" => $row['clicks'],
      "date" => $row['clicked_date'],
    );
  }
  if ($custom_fild_heading) {

    if ($chstom_filed == 'null') {
      foreach ($custom_fild_heading as $key => $value) {
        $datafield[$key] = '';
      }
    } else {
      foreach ($chstom_filed as $key => $value) {
        $datafield[$key] = $value;
      }
    }
  }
  $data[] = $datafield;
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data

);

echo json_encode($response);