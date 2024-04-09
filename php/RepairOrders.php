<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    header("Access-Allow-Control-Origin: *");

    $method = $_SERVER['REQUEST_METHOD'];   // See if it is a GET, POST, DELETE, etc

     switch($method){

        case 'POST':
//            echo 'POST';
            $rcvdJson = file_get_contents('php://input');
            //echo $rcvdJson;
            $data = json_decode($rcvdJson);
            ProcessPOST($data);
            break;

        case "GET":
  //         echo 'GET';
//           $id = $_GET["id"];
            $id = "Estimators";
           $repairs = ProcessGET($id);
           echo json_encode($repairs);
           break;

        default:
            break;
    }   // switch()


    function ProcessGET($grouping){

        require('db_open.php');

        $records = null;

//        if ($id > ''){
//            ;// Get just one record
//        } else {

        $sql = "SELECT RONum, Owner, Technician, Estimator " .
                "FROM Repairs ORDER BY Estimator, Owner";
            try{

                $s = mysqli_query($conn, $sql);
                $rows = array();

                while($r = mysqli_fetch_assoc($s)){
                    $rows[] = $r;
                }

                return $rows;

            } catch(Exception $e){

                echo "Fetching repairs failed." . $e->getMessage();

            } finally {
                //echo "reached finally";
                $conn = null;
            }   // try-catch{}
//        }   // if-else {}
    }   // ProcessGET()


    function ProcessPOST($delivery){

        require('db_open.php');

        $tsql = "INSERT INTO Deliveries " .
                "(RONum, Location, Customer, Vehicle, Technician, Vendor, Notes) " .
                "VALUES ($delivery->RONum, '$delivery->Location', " .
                         "'$delivery->Customer', '$delivery->Vehicle', " .
                         "'$delivery->Technician', '$delivery->Vendor', " .
                         "'$delivery->Notes')";
       //echo $tsql;
       try{
           $result = $conn->query($tsql);
           echo "New Delivery added successfully!";
       } catch (PDOException $pe){
           echo "New Delivery was not added to database." . $pe->getMessage();
       }

       $conn = null;        // close the database
   }    // ProcessPOST()


?>
