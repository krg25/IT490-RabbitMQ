#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
ini_set('display_errors', 'On');


function doLogin($username,$password)
{
	require_once('Logger.php.inc');
	$logger = new logger("DBListener->doLogin",'rabbit.ini');
	if (!isset($dbc)){
	require('mysqli_connect.php');
	}
	$q = "SELECT * FROM SiteUsers WHERE (username='$username' AND password='$password')";
	$r = @mysqli_query($dbc, $q);
	$num = @mysqli_num_rows($r);
	$report = "";

	if (empty(mysqli_error($dbc))){
		if ($num == 1){
		mysqli_close($dbc);
		$logger->logDebug('Success','Logged in Successfully');
		return true;
		}
		else
		{
			$logger->logDebug('Login Failure',"Invalid Credentials");
		mysqli_close($dbc);
		return false;
		}
	}
	else
	{
		$logger->logError("SQL Error","ERROR",mysqli_error($dbc));
		mysqli_close($dbc);
		return false;
	}

}
function doRegister($username,$password,$email,$fname,$lname)
{
	
	require_once('Logger.php.inc');
	$logger = new logger("DBListener->doRegister",'rabbit.ini');
	if (!isset($dbc)){
	require('mysqli_connect.php');
	}
	$q = "INSERT INTO SiteUsers (username, password, email, first_name, last_name) VALUES ('$username', '$password', '$email', '$fname', '$lname')";
	$r = @mysqli_query($dbc, $q);
	$num = @mysqli_num_rows($r);
	$report = "";

	if (empty(mysqli_error($dbc))){
		mysqli_close($dbc);
		$logger->logDebug('Success',"New Registration");
		return true;
		}
		else
		{
			$logger->logDebug("Success", mysqli_error($dbc).PHP_EOL);
		mysqli_close($dbc);
		return false;
		}
	


}

function requestProcessor($request)
{
  require_once('Logger.php.inc');
  $logger = new logger("DBListener->requestProcessor",'rabbit.ini');

  echo "received request".PHP_EOL;
  var_dump($request);


  if(!isset($request['type']))
  {
    return $logger->logError("Invalid Request","ERROR","Received an invalid request type.";
  }
  switch ($request['type'])
  {
    case "Login":
	    if(doLogin($request['username'],$request['password'])){
	$logger->logDebug("Success","Successful Login");
	  return array("returnCode" => '1', 'message'=>"Successful Login");
	}
	else
	{
	$logger->logDebug("Failure","Failed Login");
	  return array("returnCode" => '2', 'message'=>"Unsuccessful Login");
	}
    

	
     case "Register":
     if(doRegister($request['username'],$request['password'],$request['email'],$request['fname'],$request['lname'])){
	$logger->logDebug("Success","Successful Registration");
	  return array("returnCode" => '1', 'message'=>"Successful Registration");
	}
	else
	{
	$logger->logError("Failure","ERROR","Unsuccessful Registration");
	  return array("returnCode" => '2', 'message'=>"Unsuccessful Registration");
	}


    case "validate_session":
      return doValidate($request['sessionId']);
  }

	$logger->logError("Bad Message Type","ERROR","Unsupported Message Type");
   return array("returnCode" => '0', 'message'=>"Error, unsupported message type");
}

$server = new rabbitMQServer("rabbit.ini","database");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>

