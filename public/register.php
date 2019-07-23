<?php
//registering users
require_once dirname( __FILE__ ) .'/../private/header.php';

$user = new User();
$mail_class = new Mail();

//once they're registered they'll get automatically logged in, so we can stop the code here if the session is set.
if(isset($_SESSION['login']))
{
    exit('Registered! Remember to check your inbox and validate your email.');
}
?>


<form method='post'>
    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
    <input type='text' name='username' placeholder='Username'>
    <input type='text' name='mail' placeholder='Email'>
    <input type='text' name='mail2' placeholder='Confirm email'>
    <input type='password' name='password' placeholder='Password'>
    <input type='password' name='password2' placeholder='Confirm password'>
    <input type='submit' name='submit' value='Register' />
</form>


<?php
//checking for user input
if (isset($_POST['submit']))
{
    //approving user input
    extract($_POST);

    $length = $_POST['password'];

    $token = $_POST['token'];

    if($_POST['mail'] != $_POST['mail2'])
    {
        exit('Email addresses does not match.');
    }
    elseif($_POST['password'] != $_POST['password2'])
    {
        exit('Passwords do not match.');
    }
    elseif(strlen($_POST['password']) < 8)
    {
        echo strlen($_POST['password']);
        exit('Password needs to be minimum 8 characters long.');
    }
    elseif($user->verify_token($token))
    {
        extract($_POST);
        $mail = filter_var($mail, FILTER_SANITIZE_EMAIL);

        //if everything is approved, we'll call the actual function.
        if(filter_var($mail, FILTER_VALIDATE_EMAIL))
        {
            $register = $user->register_user($username, $mail, $password);

            //if the user registered successfully, log them in and send them the verification mail.
            if($register)
            {
                $user->check_login($username, $password);
                $mail_class->send_verification($mail);
                echo '<meta http-equiv="refresh" content="0">';
            }
            else
            {
                echo 'Error. Email or username already in use.';
            }
        }
        else
        {
            $mail = $user->specialchars($mail);
            exit($mail . ' is not a valid email address!');
        }
    }
}
?>