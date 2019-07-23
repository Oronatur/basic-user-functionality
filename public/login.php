<?php
require_once dirname( __DIR__ ) .'/private/header.php';
$user = new user();

//checking for user input
if (isset($_REQUEST['submit']))
{
    $token = $_POST['token'];

    if($user->verify_token($token))
    {
        extract($_POST);
        $login = $user->check_login($username, $password);

        if ($login)
        {
            // Successful login
            header("location:/../index.php");
        }
    }
}
?>

<form action="" method="post" name="login">
    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
    <input type="text" name="username" required="" placeholder="Username"/>
    <input type="password" name="password" required="" placeholder="Password"/>
    <input type="submit" name="submit" value="Login" />
</form>

<p><a href="forgot_password.php">Forgot password?</a></p>