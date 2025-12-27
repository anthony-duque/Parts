<?php

    require('db_open.php');

    class Login{

        public $username;
        public $password;

        function __construct($rec){
            $this->username = $rec["location_code"];
            $this->password = $rec["pass_code"];
        }
    }   // Login{}

    $username = $_GET['user_name'];
    $password = $_GET['pass_word'];

    $sql = <<<strSQL
                SELECT
                    id
                FROM Location_IDs
                WHERE location_code = '$username' 
                    AND pass_code = '$password';
            strSQL;

 //   echo $sql;

    try{

        $s = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($s);

        $loginSuccessful = false;

        if($r){
            $loginSuccessful = true;
        }

        echo json_encode($loginSuccessful);

    } catch(Exception $e){

        echo "Fetching Login failed." . $e->getMessage();

    } finally {

        $conn = null;

    }   // try-catch{}


?>