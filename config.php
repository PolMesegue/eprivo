<?php


define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'ormdb');
define('DB_PASSWORD', 'e7rzRQRdw!9rSr6#7mu!jyUAGEZU9r');
define('DB_NAME', 'ORM');



/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
