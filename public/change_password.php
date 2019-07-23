<?php
require_once dirname( __DIR__ ) .'/private/header.php';

$user = new user();
$password_class = new password();
?>

<form method='post'>
    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
    <input type='password' name='cpassword' placeholder='Old password'>
    <input type='password' name='npassword' placeholder='New password'>
    <input type='password' name='npassword2' placeholder='Confirm new password'>
    <input type='submit' name='submit' value='Register' />
</form>

<?php
//checking for user input

if (isset($_POST['submit']))
{
    $token = $_POST['token'];

    if($user->verify_token($token))
    {
        extract($_POST);
        $passwordchange = $password_class->set_new_password ($npassword, $npassword2, $cpassword);
    }
}

?>
