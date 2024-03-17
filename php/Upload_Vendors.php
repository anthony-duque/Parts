<?php

    const FILENAME = "../extract_files/Vendors.csv";

    const NAME 		= 0;
    const PHONE 	= 1;
    const FAX 		= 2;
    const ADDRESS 	= 3;
    const CITY 		= 4;
    const STATE 	= 5;
    const ZIPCODE 	= 6;
    const PREFERRED = 7;
    const ELECTRONIC = 8;

    if (($handle = fopen(FILENAME, "r")) == FALSE) {

        echo "Cannot open file " . FILENAME . ".";
        exit;
    }

    $tsql = "INSERT INTO Vendors " .
            "(name, phone, fax, address," .
            " city, state, zipcode, preferred, electronic) " .
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

            if (strtoupper($data[PREFERRED]) == "TRUE"){
                $preferred = 1;     // TRUE
            } else {
                $preferred = 0;     // FALSE
            }

            if ($data[ELECTRONIC] == "ELECTRONIC"){
                $electronic	  = 1;  // TRUE
            } else {
                $electronic	  = 0;  // FALSE
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
    echo $tsql;

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
