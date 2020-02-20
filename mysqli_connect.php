<?php 
$dbc = @mysqli_connect('localhost', 'connectionuser', 'conn3ctpass', 'StockApp') OR die ('Could not connect: '.mysqli_connect_error());
mysqli_set_charset($dbc, 'utf8');
?>
