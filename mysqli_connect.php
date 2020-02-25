#!/usr/bin/php
<?php 
require_once('ErrorLogger.php.inc');

$log = new ErrorLogger('mysqli_connect','rabbit.ini');


$dbc = @mysqli_connect('localhost', 'connectionuser1', 'conn3ctpass', 'StockApp') OR die ($log->logError("Connection Failure","ERROR","Failed to connect: ".mysqli_connect_error()));

mysqli_set_charset($dbc, 'utf8');
?>
