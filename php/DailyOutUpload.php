<?php

	const FILENAME = "../extract_files/Daily_Out_Report.csv";

	const RO_NUM           = 0;
	const OWNER            = 1;
	const VEHICLE_IN       = 2;
	const VEHICLE          = 3;
	const ESTIMATOR        = 4;
	const CURRENT_PHASE    = 6;
	const PARTS_RCVD       = 9;
	const TECHNICIAN       = 13;

?>

<table>
<tr>
	<th>RO</th>
	<th>Owner</th>
    <th>Vehicle</th>
    <th>Estimator</th>
	<th>Vehicle In</th>
	<th>Current Phase</th>
	<th>Parts Received</th>
	<th>Technician</th>
</tr>

<?php

$row = 0;
if (($handle = fopen(FILENAME, "r")) !== FALSE) {

    while (($data = fgetcsv($handle, 500, ",")) !== FALSE) {

        $second_field = trim($data[1]);

        if (($second_field == '') || ($second_field == 'Owner')) {
            continue;
        }

        ++$row;

		$ro_num 		= $data[RO_NUM];
		$owner 			= $data[OWNER];
		$vehicle 		= $data[VEHICLE];
		$vehicle_in 	= $data[VEHICLE_IN];
		$estimator		= $data[ESTIMATOR];
		$current_phase 	= $data[CURRENT_PHASE];
		$parts_received = $data[PARTS_RCVD];
		$technician     = $data[TECHNICIAN];
?>
		<tr>
			<td><?= $ro_num ?></td>
			<td><?= $owner ?></td>
			<td><?= $vehicle ?></td>
            <td><?= $estimator ?></td>
            <td><?= $vehicle_in ?></td>
			<td><?= $current_phase ?></td>
			<td><?= $parts_received ?></td>
			<td><?= $technician ?></td>
		</tr>
<?php

    }

    fclose($handle);
}
?>
</table>
<br/>
Total Records Read: <?= $row ?>
