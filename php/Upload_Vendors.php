<?php

    const FILENAME = "../extract_files/Vendors.csv";

    const ACTIVE 	= 0;
    const NAME 		= 1;
    const OEM 		= 2;
    const PHONE 	= 3;
    const ADDRESS 	= 4;
    const CITY 		= 5;
    const STATE 	= 6;
    const ZIPCODE 	= 7;
    const DISCOUNT  = 8;
    const EMAIL     = 9;

    if (($handle = fopen(FILENAME, "r")) == FALSE) {

        echo "Cannot open file " . FILENAME . ".";
        exit;
    }

    $tsql = "INSERT INTO Vendors " .
            "(active, name, phone, address," .
            " city, state, zipcode, oem, discount) " .
            "VALUES ";

    $row = 0;

    while (($data = fgetcsv($handle, 500, ",")) !== FALSE){

        ++$row;

        if ($row == 1){
            $num = count($data);
        } else {

            $name         = str_replace("'", "\'", $data[NAME]);
    		$phone        = $data[PHONE];
    		$fax 		  = $data[FAX];
    		$address      = str_replace("'", "\'", $data[ADDRESS]);
    		$city		  = str_replace("'", "\'", $data[CITY]);
    		$state 	      = $data[STATE];
    		$zipcode 	  = $data[ZIPCODE];

            if (strtoupper($data[ACTIVE]) == "TRUE"){
                $active = 1;     // TRUE
            } else {
                $active = 0;     // FALSE
            }

            if ($data[OEM] == [OEM]){
                $oem	  = 1;  // TRUE
            } else {
                $oem	  = 0;  // FALSE
            }

            $values = "('". $name . "', '" . $phone . "', '" . $fax . "', '". $address . "', " .
                    "'" . $city . "', '" . $state . "', '" .$zipcode . "', " .
                    $preferred . ", ". $electronic . ")";

            $tsql = $tsql . $values;
        }
    }

    fclose($handle);

    $tsql = str_replace(")(", "),(", $tsql);
    $tsql = $tsql . ';';
    //echo $tsql;

    $servername = "localhost";
    $username = "root";
    $password = "Al@d5150";
    $dbname = "CarStar";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    } else {
        echo("Connection successful!");
    }

    /*    $sql = "INSERT INTO MyGuests (firstname, lastname, email)
    VALUES ('John', 'Doe', 'john@example.com')";
    */

    if ($conn->query($tsql) === TRUE) {
      echo $row . " vendors successfully uploaded!";
    } else {
      echo "Error: " . $tsql . "<br>" . $conn->error;
    }

    $conn->close();

?>
