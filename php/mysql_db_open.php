<?php

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
        //echo("Connection successful!");
    }

    $sql = "SELECT id, name FROM Vendors";
    $s = mysqli_query($conn, $sql);
    $rows = array();

    while($r = mysqli_fetch_assoc($s)){
        $rows[] = $r;
    }

    echo json_encode($rows);

//    $stmt = $conn->connection->prepare($sql);
//    $stmt->execute();
//    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //$result = $conn->query($sql);
    //$records = $result->fetch(PDO::FETCH_ASSOC);
    //echo json_encode($result);
    /*
    if ($result->num_rows > 0){

        while($row = $result->fetch_assoc()){
            echo "RONum = " . $row["RONum"];
        }
    } else {
        echo "No result.";
    }

    */

    $conn->close();
?>
