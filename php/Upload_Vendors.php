<?php

    const LOCATION      = 0;
    const NAME          = 1;
    const AFTERMKT      = 2;
    const OEM           = 3;
    const OPT_OEM       = 4;
    const PHONE         = 5;
    const ADDRESS       = 6;
    const CITY          = 7;
    const STATE         = 8;
    const ZIPCODE 	    = 9;
    const PREFERRED     = 10;
    const ELECTRONIC    = 11;
    const VENDOR_ID     = 12;
    const EMAIL         = 13;

    const TARGET_DIR    = "../extract_files/";  // destination folder on the server
    const CSV_FILENAME  = "Vendors.csv";      // Vendors file name

    try{

        $extractFile = TARGET_DIR . CSV_FILENAME;
        $upload_OK = move_uploaded_file($_FILES["VendorFile"]["tmp_name"], $extractFile);

    } catch(Exception $e){
        echo "There was an error uploading the " . basename($_FILES["VendorFile"]["name"]);
//        header("Location: ../Upload_Vendors.html");
    } finally {
        if ($upload_OK){
            Upload_Vendors_Extract($extractFile);
            echo "<br/> Vendors upload successful!";
        }
    }


function Upload_Vendors_Extract($csv_file){

    if (($handle = fopen($csv_file, "r")) == FALSE) {
        echo "Cannot open file " . $csv_file . ".";
        exit;
    }   // if (($handle...))

        // Skip header rows
    for($i = 0; $i < 7; ++$i){
        $data = fgetcsv($handle, 500, ",");
    }   // for($i...)

    $tsql = <<<strSQL
                INSERT INTO Vendors
                    (shop_location, name, aftermarket,
                    oem, opt_oem, phone_number, address,
                    city, state, zipcode, preferred,
                    vendor_id)
                VALUES
            strSQL;

    $row = 0;

        // Then start reading records
    while (($data = fgetcsv($handle, 500, ",")) !== FALSE){

        ++$row;

        if ($row == 1){
            $num = count($data);
        } else {

            $location     = $data[LOCATION];
            $name         = str_replace("'", "\'", $data[NAME]);
            $name         = str_replace('�', '-', $name);
    		$aftermarket  = $data[AFTERMKT];
            $oem          = $data[OEM];
            $opt_oem      = $data[OPT_OEM];
            $phone        = $data[PHONE];
    		$address      = str_replace("'", "\'", $data[ADDRESS]);
    		$city		  = str_replace("'", "\'", $data[CITY]);
    		$state 	      = $data[STATE];
    		$zipcode 	  = $data[ZIPCODE];
            $preferred    = $data[PREFERRED];
            $vendor_id 	  = $data[VENDOR_ID];

            $values = <<<strSQL
                ('$location', '$name', $aftermarket,
                $oem, $opt_oem, '$phone', '$address',
                '$city', '$state', '$zipcode',
                $preferred, $vendor_id),
            strSQL;

            $tsql = $tsql . $values;
        }   // if-else
    }   // while()

    fclose($handle);

    $tsql = rtrim($tsql, ',');
    $tsql = $tsql . ';';
//    echo $tsql;

    require('db_open.php');

    if ($conn->query($tsql) === TRUE) {

      echo $row . " vendors successfully uploaded!";

      $tsql = <<<strSQL
                UPDATE Vendors v INNER JOIN Location_IDs locID
  	            SET v.location_ID = locID.id
  	            WHERE v.shop_location = locID.Location;
strSQL;

      if ($conn->query($tsql) === TRUE) {
          echo "Vendor Location IDs successfully updated!";
      }

    } else {
      echo "Vendor Upload failed: " . $tsql . "<br>" . $conn->error;
    }

    $conn->close();

}   // function Upload_Vendors_Extract()


?>
