<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


////////////////////////////////////

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

    case 'POST': // logout
        break;

    default:     // login is always GET
        Login();
        break;
}   // switch

////////////////////////////////////


function Get_Shop_IDs($companyCode, $dbConn){

    $sql = <<<strSQL
                SELECT
                    s.id
                From company_shop cs INNER JOIN location_ids s
                    ON cs.location_code = s.location_code
                WHERE cs.company_code = '$companyCode';
            strSQL;

    $retValue = "";

    try{

        $s = mysqli_query($dbConn, $sql);

        if (isset($s)){

                // Account found, store ID in cookie
            $locIDs = array();

            while($r = mysqli_fetch_assoc($s)){
                $locIDs[] = $r["id"];
            }

            $retValue = implode(',', $locIDs);

        } else {

            $retValue = "ERROR: No shops associated with $companyCode.";

        }   // if (isset($s))

    } catch(Exception $e){

        $retValue = "ERROR: Login failed." . $e->getMessage();

    } finally {

        $dbConn = null;

        return $retValue;

    }   // try-catch{}

}   // Get_Shop_IDs()


function Login(){

    $loginResult = array(
        "success" => false,
        "message" => "",
        "locationIDs" => ""
    );

    require('db_open.php');

    $username = $_GET['user_name'];
    $password = $_GET['pass_word'];

    $sql = <<<strSQL
                SELECT
                    active_end_date
                FROM companies
                WHERE company_code = '$username' 
                    AND pass_code = '$password';
            strSQL;

    try{

        $s = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($s);

        if (!isset($r)){

            $loginResult["message"] = "ERROR: Incorrect username or password.";

        } else {
                // Account found, check if active

            if(isset($r)){

                    // Login failed.  Account expired.
                if ($r["active_end_date"] < date("Y-m-d")){ 

                    $loginResult["message"] = "ERROR: Account expired.";

                } else {    // Login successful, store ID in cookie

                    $shopIDs = Get_Shop_IDs($username, $conn);

                    if (str_starts_with($shopIDs, "ERROR:")){  // No shops associated with company

                        $loginResult["message"] = $shopIDs;

                    } else {

                        $loginResult["locationIDs"] = $shopIDs;
                        $loginResult["success"] = true;
                        $loginResult["message"] = "Login successful!";
                    
                    }   // if (str_starts_with($shopIDs, "ERROR:"))
                
                }   // if ($r["active_end_date"] < date("Y-m-d"))

            } else { // Login failed.  Wrong username or password.

                $loginResult["message"] = "ERROR: Account '$username' not found.";

            }  // if(isset($r))
        }

    } catch(Exception $e){

        $loginResult["message"] = "ERROR: Login failed.(" . $e->getMessage() . ")";

    } finally {

        $conn = null;
        echo json_encode($loginResult);

    }   // try-catch{}

}

?>