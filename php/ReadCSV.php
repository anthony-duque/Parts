<?php

	const FILENAME = "../extract_files/Parts_Status_Report.csv";

	const RO_NUM = 1;
	const OWNER = 2;
	const VEHICLE = 3;
	const ESTIMATOR = 4;
	const PART_DESC = 6;
	const ORDERED_QTY = 12;
	const RECEIVED_QTY = 15;
	const RETURNED_QTY = 17;
?>

<table>
<tr>
	<th>RO</th>
	<th>Owner</th>
	<th>Vehicle</th>
	<th>Estimator</th>
	<th>Parts Description</th>
	<th>Ordered</th>
	<th>Received</th>
	<th>Returned</th>
</tr>
<?php
$row = 1;
if (($handle = fopen(FILENAME, "r")) !== FALSE) {

    while (($data = fgetcsv($handle, 500, ",")) !== FALSE) {

		if ($row == 1){
			$num = count($data);

		} else {
			$ro_num 		= $data[RO_NUM];
			$owner 			= $data[OWNER];
			$vehicle 		= $data[VEHICLE];
			$estimator 		= $data[ESTIMATOR];
			$part_desc		= $data[PART_DESC];
			$ordered_qty 	= $data[ORDERED_QTY];
			$received_qty 	= $data[RECEIVED_QTY];
			$returned_qty	= $data[RETURNED_QTY];
?>
		<tr>
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
		++$row;
    }

    fclose($handle);
}
?>
</table>
<br/>
Total Records Read: <?= $row ?>
