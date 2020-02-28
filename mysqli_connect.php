<?php 
require_once('Logger.php.inc');

$log = new Logger('mysqli_connect','rabbit.ini');


$dbc = @mysqli_connect('localhost', 'connectionuser', 'conn3ctpass', 'StockApp') OR die ($log->logError("Connection Failure","ERROR","Failed to connect: ".mysqli_connect_error()));

mysqli_set_charset($dbc, 'utf8');
?>
