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

    $username = $_POST['user_name'];
    $password = $_POST['pass_word'];

    $sql = <<<strSQL
                SELECT
                    id
                FROM Location_IDs
                WHERE location_code = '$username' 
                    AND pass_code = '$password';
            strSQL;

    echo $sql;

    try{

        $s = mysqli_query($conn, $sql);

        $r = mysqli_fetch_assoc($s);

        if($r){
            echo "Login successful.";
        } else {
            echo "Login failed.";
        }

    } catch(Exception $e){

        echo "Fetching Login failed." . $e->getMessage();

    } finally {

        $conn = null;

    }   // try-catch{}


?>