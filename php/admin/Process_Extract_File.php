<?php

const SHOP_ID       = 0;
const SHOP_NAME     = 1;
const RO_NUM        = 2;
const ESTIMATOR     = 3;
const TECHNICIAN    = 4;
const OWNER         = 5;
const VEHICLE_COLOR = 6;
const LICENSE_PLATE = 7;
const VIN           = 8;
const VEHICLE_YEAR  = 9;
const VEHICLE_MAKE  = 10;
const VEHICLE_MODEL = 11;
const CURRENT_PHASE = 12;
const DATE_IN       = 13;
const TARGET_DATE   = 14;
const LINE_NO       = 15;
const OP_CODE       = 16;
const PART_DESC     = 17;
const PART_NO       = 18;
const PART_TYPE     = 19;
const PART_STATUS   = 20;
const VENDOR_NAME   = 21;
const PART_PRICE    = 22;
const RO_QTY        = 23;
const ORDER_DATE    = 24;
const ORDERED_QTY   = 25;
const RECEIVED_QTY  = 26;
const RECEIVED_DATE = 27;
const RETURNED_QTY  = 28;

require('../Utility_Scripts.php');

function Process_Extract($extract_file, $companyID){

    $uploadSuccessful = true;   // flag to track upload success

    // Open the extract file for reading
    if (($handle = fopen($extract_file, "r")) === FALSE) {
        echo "Error in opening " . $extract_file;
        exit;
    }

    $insert_sql = <<<strSQL
            INSERT INTO extract_file_dump
                (shop_id, shop_name, ro_num, estimator, technician,
                owner, vehicle_color, license_plate, vin, vehicle_year,
                vehicle_make, vehicle_model, current_phase, date_in,
                target_date, line_num, repair_code, part_desc, 
                part_num, part_type, part_status, vendor_name, part_price,
                ro_qty, order_date, order_qty, received_qty, received_date, 
                return_qty, company_id)
            VALUES
strSQL;

    $row    = 0;  // record counter
    $values = '';

    while (($data = fgetcsv($handle, 500, ",")) !== FALSE){

        if ($row == 0){

            ++$row;
            continue;   // skip header row

        } else {

            $shop_id        = "'" . $data[SHOP_ID] . "'";

            $shop_name      = "'" . $data[SHOP_NAME] . "'";

            $ro_num         = "'" . $data[RO_NUM] . "'";

            $estimator      = "'" . Cleanup_Text($data[ESTIMATOR]) . "'";

            $technician     = "'" . Cleanup_Text($data[TECHNICIAN]) . "'";

            $owner          = "'" . Cleanup_Text($data[OWNER]) . "'";

            $vehicle_color  = "'" . $data[VEHICLE_COLOR] . "'";

            $license_plate  = "'" . $data[LICENSE_PLATE] . "'";

            $vin            = "'" . $data[VIN] . "'";

            $vehicle_year   = $data[VEHICLE_YEAR];

            $vehicle_make   = "'" . $data[VEHICLE_MAKE] . "'";

            $vehicle_model  = "'" . $data[VEHICLE_MODEL] . "'";

            $current_phase  = "'" . $data[CURRENT_PHASE] . "'";

            $date_in        = Get_SQL_date($data[DATE_IN]);

            $target_date    = Get_SQL_date($data[TARGET_DATE]);

            $line_no        = $data[LINE_NO];

            $op_code        = "'" . $data[OP_CODE] . "'";

            $part_desc      = "'" . Cleanup_Text($data[PART_DESC]) . "'";

            $part_no        = "'" . Cleanup_Text($data[PART_NO]) . "'";

            $part_type      = "'" . $data[PART_TYPE]. "'";

            $part_status    = $data[PART_STATUS];

            $vendor_name    = "'" . Cleanup_Text($data[VENDOR_NAME]) . "'";

            $part_price     = Get_Quantity(str_replace("$", "", $data[PART_PRICE]));

            $ro_qty         = Get_Quantity($data[RO_QTY]);

            $order_date     = Get_SQL_date($data[ORDER_DATE]);

            $ordered_qty    = Get_Quantity($data[ORDERED_QTY]);

            $received_qty   = Get_Quantity($data[RECEIVED_QTY]);

            $received_date  = Get_SQL_date($data[RECEIVED_DATE]);

            $returned_qty   = Get_Quantity($data[RETURNED_QTY]);

            $values .= "(" . $shop_id . ", " . $shop_name . ", " . $ro_num . ", " .
                        $estimator . ", " . $technician . ", " . $owner . ", " .
                        $vehicle_color . ", " . $license_plate . ", " . $vin . ", " .
                        $vehicle_year . ", " . $vehicle_make . ", " . $vehicle_model . ", " .
                        $current_phase . ", " . $date_in . ", " . $target_date . ", " .
                        $line_no . ", " . $op_code. ", " . $part_desc. ", " .
                        $part_no. ", " . $part_type. ", '" .$part_status. "', ".
                        $vendor_name. ", ". $part_price. ", ". $ro_qty. ", ".
                        $order_date. ", ". $ordered_qty. ", ". $received_qty. ", ".
                        $received_date. ", ". $returned_qty . "," .
                        $companyID . "),";
  
            ++$row;

        }   // if ($row == 0)

    }   // while (($data = fgetcsv($handle, 500, ",")) !== FALSE)

    $values = rtrim($values, ",");   // remove trailing comma
    $insert_sql .= $values;

  //  echo $insert_sql . '<br/><br/>';

    require('../db_open.php');

        // Delete all records from the Extract table for the company ID.
    $tsql = "DELETE FROM extract_file_dump " . 
            "WHERE company_id = $companyID";

    try{

        if ($conn->query($tsql) === TRUE) {
            echo "<br/><br/>Extract records for $companyID deleted.<br/>";
        } 

        if ($conn->query($insert_sql) === TRUE) {
            echo "<br/><br/> $row extract records for $companyID uploaded successfully.<br/>";
        }

    } catch(Exception $e){

        echo "Error in inserting/deleting extract records for $companyID: " . $e->getMessage();
        $uploadSuccessful = false;   // set flag to false if an error occurs

    } finally {

        $conn->close();
        return $uploadSuccessful;   // return the status of the upload operation
    }
}
?>