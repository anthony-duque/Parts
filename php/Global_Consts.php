<?php

    final class Global_Consts{

        private static ?mysqli $dbConn = null;
        private static $dateRangeStr = '';

        public static function getConn() : mysqli{

            if (is_null(self::$dbConn)){  // to make sure connection is only once.

                require('db_open.php');
                self::$dbConn = $conn;

            }   // if(is_null)
            
            return self::$dbConn;

        }   // getConn()


        public static function closeConn(){

            if(self::$dbConn){
                self::$dbConn->close();
            }
        }   // closeConn()


        public static function getDateRangeStr()
        {
            if (empty(self::$dateRangeStr)){

                $numDays = $_GET["numDays"];

                $dateClause = '';

                if ($numDays > 1){
                    $dateClause = "invoice_date IS NOT NULL";
                } else {
                    $dateClause = "DATEDIFF(CURDATE(), Invoice_Date) = $numDays";
                }

                self::$dateRangeStr = $dateClause;
            }   // if (empty...)

            return self::$dateRangeStr;

        }   // getDateRangeStr()

    
        private function __construct(){}

    }   // Global_Constants{} 
?>