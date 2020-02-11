<?php 
$dbc = @mysqli_connect('localhost', 'connectionuser', 'conn3ctpass', 'SiteDirectory') OR die ('Could not connect: '.mysqli_connect_error());
mysqli_set_charset($dbc, 'utf8');
?>
