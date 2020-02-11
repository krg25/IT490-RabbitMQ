#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doLogin($username,$password)
{
    //SQL query here we should probably salt passwords
	//select * from users where USERNAME = $username, PASSWORD = $password
	//if result.length = 1 (one response from sql)
	if ($username == "ken"){
    	return true;
	}
	else
	{
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

