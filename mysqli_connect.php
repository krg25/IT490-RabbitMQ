<?php 
$dbc = @mysqli_connect('localhost', 'krg', 'password', 'krg25') OR die ('Could not connect: '.mysqli_connect_error());
mysqli_set_charset($dbc, 'utf8');
?>
