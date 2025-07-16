<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'Utility_Scripts.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method){

   case 'POST':;
      $json = file_get_contents('php://input');
      $data = json_decode($json);
      ProcessPOST($data);
      break;

    default:
        ProcessGET();
        break;
}   // switch

////////////////////////////////////

function Get_RO_Name_By_Role($role, $locID, $roNum = 0){

    require('db_open.php');
    $name = '';

    $sql = "SELECT " . strtolower($role) . " FROM Repairs " .
            "WHERE RONum = " . $roNum . " AND Loc_ID = " . $locID;
    // echo $sql;
    try{

        $s = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($s);
        $name = $r[strtolower($role)];
        var_dump($name);

    } catch(Exception $e){

        echo "Getting " . $role . " for RO " . $roNum . " failed.";

    } finally {
        $conn = null;
        return $name;
    }   // try-catch
}       // Get_RO_Name()


function ProcessPOST($email){

    $to_first_name = '';
    $cc = '';

    if($email->ro_num > 0){
        $to_name = Get_RO_Name_By_Role($email->to, $email->loc_id, $email->ro_num);
        $to_first_name = explode(" ", $to_name)[0];
    }

    $to = Get_Email_Address($email->to, $email->loc_id, $to_first_name);
    $cc = Get_Email_Address($email->cc, $email->loc_id, '');;

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $headers .= "From: Automated Email<donotreply@cityautobody.net>\r\n";

    if ($cc > ''){
        $headers .= "Cc: " . $cc . "\r\n";
    }

    echo $to . "\r\n";
    echo $cc . "\r\n";
    echo $headers . "\r\n";

    if ($to > ''){
        mail($to, $email->subject, $email->body, $headers);
        echo "Notification successful!";
    } else {
        echo "Notification to " . $email->to . "  RO: " . $email->ro_num . " failed.";
    }   // if ($to...)

}   // ProcessPOST()

?>
