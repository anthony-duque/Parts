
<?php

const RO_NUM = 1;
const OWNER = 2;
const VEHICLE = 3;
const ESTIMATOR = 4;
const PART_DESC = 6;
const ORDERED_QTY = 12;
const RECEIVED_QTY = 15;
const RETURNED_QTY = 17;

$row = 1;
if (($handle = fopen("Parts_Status_Report.csv", "r")) !== FALSE) {

?>
<table>
<tr>
	<th>RO</th>
	<th>Owner</th>
	<th>Vehicle</th>
	<th>Estimator</th>
	<th>Part Description</th>
	<th>Ordered Qty</th>
	<th>Received Qty</th>
	<th>Returned Qty</th>
</tr>

<?php

    while (($data = fgetcsv($handle, 500, ",")) !== FALSE) {
?>
<tr>
<?php
        $num = count($data);
        //echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;

		$ro_num 		= $data[RO_NUM];
		$owner 			= $data[OWNER];
		$vehicle 		= $data[VEHICLE];
		$estimator 		= $data[ESTIMATOR];
		$part_desc		= $data[PART_DESC];
		$ordered_qty 	= $data[ORDERED_QTY];
		$received_qty 	= $data[RECEIVED_QTY];
		$returned_qty	= $data[RETURNED_QTY];
?>
	<td><?= $ro_num ?></td>
	<td><?= $owner ?></td>
	<td><?= $vehicle ?></td>
	<td><?= $estimator ?></td>
	<td><?= $part_desc ?></td>
	<td><?= $ordered_qty ?></td>
	<td><?= $received_qty ?></td>
	<td><?= $returned_qty ?></td>
</tr>
<?php		

    }    
    
    fclose($handle);
}
?>
</table>
<br/>
Total Records Read: <?= $row ?>
