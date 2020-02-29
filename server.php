#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
ini_set('display_errors', 'On');


function getUserInfo($id){
        if (!isset($dbc)){
        require('mysqli_connect.php');
        }
        $q = "SELECT * FROM SiteUsers WHERE (user_id='$id')";
        $r = @mysqli_query($dbc, $q);
        $num = @mysqli_num_rows($r);
	$report = mysqli_fetch_assoc($r);

	if (empty(mysqli_error($dbc))){
           if ($num == 1){
		$out['0']=true;
		$out['id']=$report['user_id'];
		$out['user']=$report['username'];
		$out['fname']=$report['first_name'];
		$out['lname']=$report['last_name'];
		$out['date']=$report['date_joined'];
        	mysqli_close($dbc);
                return $out;
            }
            else
            {
                $out['0']=false;
                mysqli_close($dbc);
                return $out;
            }
        }

}
function doLogin($username,$password)
{
	if (!isset($dbc)){
	require('mysqli_connect.php');
	}
	$q = "SELECT * FROM SiteUsers WHERE (username='$username' AND password='$password')";
	$r = @mysqli_query($dbc, $q);
	$num = @mysqli_num_rows($r);
	$report = mysqli_fetch_assoc($r);

	if (empty(mysqli_error($dbc))){
		if ($num == 1){
			$out['0'] = true;
			$out['id']=$report['user_id'];
		mysqli_close($dbc);
		echo "Valid login!\n";
		return  $out;
		}
		else
		{
		echo "Error: Incorrect login\n";
		mysqli_close($dbc);
			$out['0'] = false;
		return  $out;
		}
	}
	else
	{
		echo "SQL Error: ".mysqli_error($dbc)."\n";
		mysqli_close($dbc);
			$out['0'] = false;
		return  $out;
	}

}
function doRegister($username,$password,$email,$fname,$lname)
{
	if (!isset($dbc)){
	require('mysqli_connect.php');
	}
	$q = "INSERT INTO SiteUsers (username, password, email, first_name, last_name) VALUES ('$username', '$password', '$email', '$fname', '$lname')";
	$r = @mysqli_query($dbc, $q);
	$num = @mysqli_num_rows($r);
	$report = "";

	if (empty(mysqli_error($dbc))){
		$q = "SELECT * FROM SiteUsers WHERE (username='$username' AND password='$password')";
		$r = @mysqli_query($dbc, $q);
		$num = @mysqli_num_rows($r);
		$report = mysqli_fetch_assoc($r);
			$out['0'] = true;
			$out['id']=$report['user_id'];
		mysqli_close($dbc);
		echo "New Registration!\n";
		return  $out;
		}
		else
		{
		echo "A registration resulted in an error: ".mysqli_error($dbc).PHP_EOL;
		$out['0']=false;
		mysqli_close($dbc);
		return $out;
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
    $login=doLogin($request['username'],$request['password']);
     if($login['0']){
	return array("returnCode" => '1', 'message'=>"Successful Login", 'ID' => $login['id']);	
	}
	else
	{
	  return array("returnCode" => '2', 'message'=>"Unsuccessful Login");
	}
    

	//Ken was here, change this to operate like login.
     case "Register":
     $register=doRegister($request['username'],$request['password'],$request['email'],$request['fname'],$request['lname']);
     if($register['0']){
	  return array("returnCode" => '1', 'message'=>"Successful Registration", 'ID' => $register['id']);
	}
	else
	{
	  return array("returnCode" => '2', 'message'=>"Unsuccessful Registration");
	}


     case "Profile":
	$userinfo=getUserInfo($request['ID']);
	if($userinfo['0']){
	

	return array(
	"returnCode" => '1',
	"id" => $userinfo['id'],
	"username" => $userinfo['user'],
	"fname" => $userinfo['fname'],
	"lname" => $userinfo['lname'],
	"date" => $userinfo['date']
	);

	}else{
	return array("returnCode" => '2');
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

