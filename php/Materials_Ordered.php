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
//          $ordersList = ProcessGET();
//          echo json_encode($ordersList);
          break;

       case "DELETE":
          $qString = $_GET["id"];
          echo "DELETE = " . $qString;
          break;

       default:
          break;
    }


    function ProcessPOST($order){

        $orders = <<<strHTML
                    <html>
                        <table border='1' width='50%'>
                        <tr>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Part Number</th>
                            <th>Brand</th>
                            <th>Description</th>
                            <th>Received</th>
                        </tr>
                strHTML;

        $bkgrndColor = "powderblue";

        foreach($order->materials as $eachMaterial){

                // toggle the color of the rows
            if ($bkgrndColor == "powderblue"){
                $bkgrndColor = "white";
            } else {
                $bkgrndColor = "powderblue";
            }

            $received = "No";
            if ($eachMaterial->received == 1){
                $received = "Yes";
            }
            $eachLine = <<<strLine
                            <tr style='background-color:$bkgrndColor;'>
                                <td align='center'>$eachMaterial->ordered_qty</td>
                                <td align='center'>$eachMaterial->unit</td>
                                <td align='center'>$eachMaterial->part_number</td>
                                <td align='center'>$eachMaterial->brand</td>
                                <td align='center'>$eachMaterial->description</td>
                                <td align='center'>$received</td>
                            </tr>
                        strLine;
            $orders .= $eachLine;
        }   // foreach()

        $orders .= "</table></html>";

        // echo $orders;
        if ($order->locationID == 1){
            $to = "Parts Department<parts@cityautobody.net>, CarStar<carstarsimivalley@gmail.com>";
        } else {
            $to = "Parts Department<SonnyParts@carstarusa.com>";
        }

        $subject    = "Materials Order from " . $order->technician;

        $headers = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\n";
        $headers .= "From: Automated Email <parts@cityautobody.net>\n";
        $headers .= "Cc: Jim <jimd@cityautobody.net>, Chad<chadwhite@cityautobody.net>";

        try {
            mail($to, $subject, $orders, $headers);
            echo "Email sent successfully!";
        } catch (Exception $e) {
            echo "Email send failed! ($e->getMessage())";
        }
    }   // ProcessPOST()

//phpinfo();
?>
