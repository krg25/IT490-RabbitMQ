#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
ini_set('display_errors', 'On');

function doLogin($username,$password)
{
	if (!isset($dbc)){
	require('mysqli_connect.php');
	}
	$q = "SELECT * FROM users WHERE (username='$username' AND password='$password')";
	$r = @mysqli_query($dbc, $q);
	$num = @mysqli_num_rows($r);
	$report = "";

	if (empty(mysqli_error($dbc))){
		if ($num == 1){
		mysqli_close($dbc);
		echo "Valid login!\n";
		return true;
		}
		else
		{
		echo "Error: Incorrect login\n";
		mysqli_close($dbc);
		return false;
		}
	}
	else
	{
		echo "SQL Error: ".mysqli_error($dbc)."\n";
		mysqli_close($dbc);
		return false;
	}

}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);


  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "Login":
     if(doLogin($request['username'],$request['password'])){
	  return array("returnCode" => '1', 'message'=>"Successful Login");
	}
	else
	{
	  return array("returnCode" => '2', 'message'=>"Unsuccessful Login");
	}


    case "validate_session":
      return doValidate($request['sessionId']);
  }

   return array("returnCode" => '0', 'message'=>"Error, unsupported message type");
}

$server = new rabbitMQServer("rabbit.ini","database");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

