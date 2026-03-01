<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function deleteCookie($name) {

    unset($_COOKIE[$name]);

    setcookie($name, "", [
        'expires' => time() + (60 * 60 * 8),  // = 8 Hours
        'path' => '/',
        'secure' => true    // only send cookie over secure connections
//                'httponly' => true, 
//                'samesite' => 'Strict'
    ]);

}   // deleteCookie()

////////////////////////////////////

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

    case 'POST': // logout
        deleteCookie("locationID");
        break;

    default:                // login is always GET
        Login();
        break;
}   // switch

////////////////////////////////////

function Login(){

    require('db_open.php');

    $username = $_GET['user_name'];
    $password = $_GET['pass_word'];

    $sql = <<<strSQL
                SELECT
                    id, active_end_date
                FROM Location_IDs
                WHERE location_code = '$username' 
                    AND pass_code = '$password';
            strSQL;

 //   echo $sql;

    try{

        $s = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($s);

        $loginSuccessful = false;

        if(isset($r)){

            if (is_null($r["active_end_date"]) || ($r["active_end_date"] >= date("Y-m-d")) ){ 

                    // active_end_date is either NULL (no end date) or in the future
                $loginSuccessful = true;

                setcookie("locationID", $r["id"], [
                    'expires' => time() + (60 * 60 * 8),  // = 8 Hours
                    'path' => '/',
                    'secure' => true    // only send cookie over secure connections
                    //                'httponly' => true, 
                    //                'samesite' => 'Strict'
                ]); 

            } else {
                
                deleteCookie("locationID");

            }   // if (is_null(...))

        } else {

            deleteCookie("locationID");

        }   // if(isset($r))

        echo json_encode($loginSuccessful);

    } catch(Exception $e){

        echo "Fetching Login failed." . $e->getMessage();

    } finally {

        $conn = null;

    }   // try-catch{}

}
?>