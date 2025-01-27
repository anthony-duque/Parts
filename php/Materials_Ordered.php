<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){

       case 'POST':;
          $json = file_get_contents('php://input');
          $data = json_decode($json);
//          echo $json;
          ProcessPOST($data);
          break;

       case "PUT":    // Could read from input and query string
          echo 'PUT';
//          ProcessPUT();
          break;

       case "GET":
            echo "GET";
//          $materialsList = ProcessGET();
//          echo json_encode($materialsList);
          break;

       case "DELETE":
          $qString = $_GET["id"];
          echo "DELETE = " . $qString;
          break;

       default:
          break;
    }


    function ProcessPOST($order){

        $orders = "<html><table>";
        foreach($order->materials as $eachMaterial){
            $eachLine = <<<strLine
                            <tr>
                                <td>$eachMaterial->ordered_qty</td>
                                <td>$eachMaterial->unit</td>
                                <td>$eachMaterial->part_number</td>
                                <td>$eachMaterial->description</td>
                            </tr>
                        strLine;
            $orders .= $eachLine;
        }

        $orders .= "</table></html>";

        $to         = "Parts Department<adduxe@gmail.com>";
//        $to         = "Parts Department<parts@cityautobody.net>";
        $subject    = "Materials Order from " . $order->technician;

        $headers = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\n";
        $headers .= "From: Automated Email <donotreply@cityautobody.net>\n";
        $headers .= "Cc: Jim <adduxe@hotmail.com>";

        $emaiSent = mail($to, $subject, $orders, $headers);
        if ($emaiSent){
            echo "Email sent successfully!";
        } else {
            echo "Email send failed!";
        }
    }   // ProcessPOST()

//phpinfo();
?>
