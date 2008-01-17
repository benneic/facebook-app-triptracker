<?php
// 
// Application: TripTracker
// File: 'index.php' 
//   This is a sample skeleton for your application. 
// 
require_once 'facebook.php';
$appapikey = 'fe50dc9527da795fd0345f2b99c643f0';
$appsecret = '8699704d19251fe37fb3ae806ea5edf7';

$facebook = new Facebook($appapikey, $appsecret);

$user_id = $facebook->require_login();

?>
<div style="padding: 10px;">

<h2>Hi <fb:name uid="<?=$user_id?>" useyou="false" />!</h2><br/>
<h2>Hi <fb:name firstnameonly="true" uid="<?=$user_id?>" useyou="false"/>!</h2><br/>

<?php

// Print out at most 25 of the logged-in user's friends,
// using the friends.get API method

echo "<p>Friends:";

$friends = $facebook->api_client->friends_get();
$friends = array_slice($friends, 0, 25);

foreach ($friends as $friend) 
{
	echo "<br>$friend";
}


?>

<div style="clear: both;"/>
</div>