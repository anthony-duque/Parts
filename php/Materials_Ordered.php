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
                            <th>Description</th>
                        </tr>
                strHTML;

        foreach($order->materials as $eachMaterial){
            $eachLine = <<<strLine
                            <tr>
                                <td align='center'>$eachMaterial->ordered_qty</td>
                                <td align='center'>$eachMaterial->unit</td>
                                <td align='center'>$eachMaterial->part_number</td>
                                <td align='center'>$eachMaterial->description</td>
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
        $headers .= "From: Automated Email <parts@cityautobody.net>\n";
        $headers .= "Cc: Jim <adduxe@hotmail.com>";

        try {
            mail($to, $subject, $orders, $headers);
            echo "Email sent successfully!";
        } catch (Exception $e) {
            echo "Email send failed! ($e->getMessage())";
        }
    }   // ProcessPOST()

//phpinfo();
?>
