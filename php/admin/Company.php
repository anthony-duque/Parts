<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
flush();

$tsql = <<<strSQL
            SELECT Company_Code, Address, Name, Pass_Code,
                Phone, Account_Start_Date, Account_End_Date,
                Contact_Person, Email
            FROM companies
        strSQL;

require('../db_open.php');

$s = mysqli_query($conn, $tsql);

while($rec = mysqli_fetch_assoc($s)){

    
}

?>