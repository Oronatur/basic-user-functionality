<?php
	require_once dirname( __DIR__ ) .'/private/header.php';
	$user = new user();
	$password_class = new password();

	//selector gets set when the reset process begins, so this will only show if they haven't initiated password reset yet.
	if(!isset($_GET['selector']))
		{
?>
<div>
<form method='post'>
	<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
	<input type='text' name='mail' placeholder='Email'>
	<input type='submit' name='submit' value='Reset password' />
</form>

<?php
		}

	if (isset($_POST['submit']))
		{
			$token = $_POST['token'];

			if($user->verify_token($token))
				{
					extract($_POST);

					$mail = filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL);

					if(isset($mail))
						{
							$password_class->reset_password($mail);
						}
					else
						{
							echo 'If there is an user connected to the email you submitted, they will recieve a mail with a reset link.';
						}
				}
		}

	echo '</div>';

	//if the tokens are set, we initiate the password change function.
	if (isset($_GET['selector']) && isset($_GET['validator']))
		{
			$selector = $user->specialchars($_GET['selector']);
			$validator = $user->specialchars($_GET['validator']);
?>
    <form action="" method="post">
		<input type="hidden" name="token2" value="<?php echo $_SESSION['token']; ?>">
        <input type="hidden" name="selector" value="<?php echo $selector; ?>">
        <input type="hidden" name="validator" value="<?php echo $validator; ?>">
		<input type="password" name="password1" placeholder="Enter your new password" required>
        <input type="password" name="password2" placeholder="Confirm your new password" required>
        <input type="submit" name="submit2" value="Submit">
    </form>

<?php
		}

	//comparing and making sure the password is up to standards.
	if(isset($_POST['submit2']))
		{
			$token2 = $_POST['token2'];

			if($user->verify_token($token2))
				{
					extract($_POST);

					$password1 = $_POST['password1'];
					$password2 = $_POST['password2'];

					if(strlen($password1) < 7)
						{
							exit('Password needs to be minimum 8 characters long.');
						}
					elseif($password1 != $password2)
						{
							exit('New passwords do not match');
						}
					elseif($password1 === $password2)
						{
							$newpassword = $password1;
							$password_class->reset_password_do($selector,$validator,$newpassword);
						}
					else
						{
							exit('Error.');
						}
				}
		}

?>