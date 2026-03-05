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

    default:     // login is always GET
        Login();
        break;
}   // switch

////////////////////////////////////


function storeIDsinCookie($companyCode, $dbConn){

    $sql = <<<strSQL
                SELECT
                    s.id
                From Company_Shop cs INNER JOIN Location_IDs s
                    ON cs.Location_Code = s.location_code
                WHERE cs.Company_Code = '$companyCode';
            strSQL;

    try{

        $s = mysqli_query($dbConn, $sql);

        if (isset($s)){

                // Account found, store ID in cookie
            $locIDs = array();

            while($r = mysqli_fetch_assoc($s)){
                $locIDs[] = $r["id"];
            }

            setcookie("locationID", implode(',', $locIDs), [
                'expires' => time() + (60 * 60 * 8),  // = 8 Hours
                'path' => '/',
                'secure' => true    // only send cookie over secure connections
//                'httponly' => true,
//                'samesite' => 'Strict'
            ]);

        } else {

            echo "No shops associated with $companyCode.";

        }
    } catch(Exception $e){

        echo "Login failed." . $e->getMessage();

    } finally {
        $dbConn = null;
    }   // try-catch{}

}   // storeIDsinCookie()


function Login(){

    require('db_open.php');

    $username = $_GET['user_name'];
    $password = $_GET['pass_word'];

    $sql = <<<strSQL
                SELECT
                    Account_End_Date
                FROM Company
                WHERE Company_Code = '$username' 
                    AND Pass_Code = '$password';
            strSQL;

    try{

        $s = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($s);

        if (!isset($r)){

            echo "Incorrect username or password.";

        } else {
                // Account found, check if active

            if(isset($r)){

                    // Login failed.  Account expired.
                if ($r["Account_End_Date"] < date("Y-m-d")){ 

                    echo "Account expired.";

                } else {    // Login successful, store ID in cookie

                    storeIDsinCookie($username, $conn);
                    echo "true";
                
                }
            } else{ // Login failed.  Wrong username or password.

                echo "Account not found.";

            }  // if(isset($r))

//            echo json_encode($loginSuccessful);
        }

    } catch(Exception $e){

        echo "Fetching Login failed." . $e->getMessage();

    } finally {

        $conn = null;

    }   // try-catch{}

}

?>