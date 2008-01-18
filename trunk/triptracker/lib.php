<?php
include_once('parsexml.php');

// Create database connection
function get_db_conn()
{
  $conn = mysql_pconnect($GLOBALS['db_ip'], $GLOBALS['db_user'], $GLOBALS['db_pass']);
  mysql_select_db($GLOBALS['db_name'], $conn);
  return $conn;
}

// Insert new user
function put_new_user($user_fb, $user_tt)
{
	$conn = get_db_conn();
	mysql_query("INSERT INTO `users` (`user_fb`,`user_tt`,`updated`) VALUES ('$user_fb','$user_tt','".date('c')."')", $conn);
}

// Insert new user
function get_tt_user($user_fb)
{
	$conn = get_db_conn();
	$result = mysql_query("SELECT `user_tt` FROM `users` WHERE (`user_fb` = '$user_fb')", $conn);
	if(mysql_num_rows($result) == 1)
	{
		list($user_tt) = mysql_fetch_array($result); 
		return $user_tt;
	}
	else
	{
		return FALSE;
	}
}

function get_journey_list($user_tt)
{
	$xmlurl = "http://triptracker.net/profile/".get_encoded_user($user_tt)."/rss/";
	print "1";
	print $xmlurl;
	// create a new cURL resource
	$ch = curl_init();
	
	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, $xmlurl);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
	
	// grab URL and pass it to the browser
	$data = curl_exec($ch);	
	// close cURL resource, and free up system resources
	curl_close($ch);
	
	print_r($data);
	
	print "2";
	$xmlparser = &new xmlParser();
	$xmlparser->parse_curl(stripslashes($xmlurl));
	return $p->root_node;
}

function get_encoded_user($user_tt)
{
	$user_tt = str_replace("&", "%26", $user_tt);
	$user_tt = rawurlencode($user_tt);
	return $user_tt;
}

?>