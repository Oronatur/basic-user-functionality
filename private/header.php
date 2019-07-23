<?php
require_once dirname( __FILE__ ) . '/autoload.php';
require_once dirname( __FILE__ ) . '/ini_config.php';

$user = new User();
session_start();

$user->session_token();

?>
		<a href="/../index.php">Home</a>

<?php
	//basic navigation bar
	if(isset($_SESSION['login']))
		{
			echo '<a href="/public/logout.php">Sign out</a>';
			echo '<a href="/public/profile.php"> Profile </a>';
			echo '<a href="/public/settings.php"> Settings </a>';
		}
	else
		{
			echo '<a href="/public/login.php">Sign in</a>';
			echo '<a href="/public/register.php">Register</a>';
		}

?>