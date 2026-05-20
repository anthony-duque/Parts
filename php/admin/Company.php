<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
flush();

$tsql = <<<strSQL
            SELECT company_code, address, name, pass_code,
                phone, account_start_date, account_end_date,
                contact_person, email
            FROM companies
        strSQL;

require('../db_open.php');

$s = mysqli_query($conn, $tsql);

while($rec = mysqli_fetch_assoc($s)){

    
}

?>