<?php

    require('db_open.php');

    $username = $_GET['user_name'];
    $password = $_GET['pass_word'];

    $sql = <<<strSQL
                SELECT
                    id
                FROM Location_IDs
                WHERE location_code = '$username' 
                    AND pass_code = '$password';
            strSQL;

 //   echo $sql;

    try{

        $s = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($s);

        $loginSuccessful = false;

        if($r){

            $loginSuccessful = true;

            setcookie("locationID", $r["id"], [
                'expires' => time() + (60 * 60 * 8),  // = 8 Hours
                'path' => '/',
                'secure' => true    // only send cookie over secure connections
//                'httponly' => true, 
//                'samesite' => 'Strict'
            ]);        
        } else {

            unset($_COOKIE["locationID"]);

            setcookie("locationID", "", [
                'expires' => time() + (60 * 60 * 8),  // = 8 Hours
                'path' => '/',
                'secure' => true    // only send cookie over secure connections
//                'httponly' => true, 
//                'samesite' => 'Strict'
            ]);        
        }

        echo json_encode($loginSuccessful);

    } catch(Exception $e){

        echo "Fetching Login failed." . $e->getMessage();

    } finally {

        $conn = null;

    }   // try-catch{}


?>