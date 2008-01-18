<?php
// 
// Application: TripTracker
// File: 'index.php' 
// 
// Benn Eichhorn
// 15 Jan 2008
// 
require_once 'facebook.php';
require_once 'config.php';
require_once 'lib.php';

$facebook = new Facebook($appApiKey, $appSecret);

$user_fb = $facebook->require_login();


if (isset($_POST['user_tt']))
{	
	put_new_user($user_fb, $_POST['user_tt']);
}

?>
<fb:if-user-has-added-app>

	<div style="padding: 10px;">
	<h2>
	Hi <fb:name firstnameonly="true" uid="<?=$user_fb?>" useyou="false"/>, welcome back to TripTracker.
	</h2>
	<?php
	
	$user_tt = get_tt_user($user_fb);
	
	// if users has linked in their tt username
	if($user_tt !== FALSE)
	{
		?>
		<br/><br/>
		You have linked <?=$user_tt?>
		<br/><br/>
		<?php
		$xml = get_journey_list($user_tt);
		print_r($xml);
		?>
		</div>
		<div style="clear: both;"/>
		</div>
		<?php
	}
	// else user hasn't added tt username yet
	else
	{
		
		?>
		<br/><br/>
		You have not yet linked your TripTracker username to your Facebook profile.
		<br/><br/>
		Enter it below:
		<br/><br/>
		<form method="post" action="http://apps.facebook.com/triptracker/">
		 <input name="user_tt" type="text"/>  <input value="Add" type="submit"/>
		</form>
		</div>
		<div style="clear: both;"/>
		</div>
		<?php
	}
	
	?>
<fb:else>

	<div style="padding: 10px;">
	<h2>
	Hi <fb:name firstnameonly="true" uid="<?=$user_fb?>" useyou="false"/>, welcome to the TripTracker application.
	</h2>
	<br/><br/>
	If you've got a TripTracker login then <a href="<?= $facebook->get_add_url() ?>">add</a> this application to your profile now!
	<br/><br/>
	Once you have linked your TripTracker account into your profile you will be able to select from your GPS tracked journeys to display them directly in your profile page and more. 
	<br/><br/>
	For more info on TripTracker visit their <a href="http://triptracker.net/">site</a>.
	<br/><br/>
	</div>

</fb:if-user-has-added-app>


