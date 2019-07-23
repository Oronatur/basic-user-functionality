<?php
//verifying email
require_once dirname( __DIR__ ) .'/private/header.php';

    $user = new User ();
    $mail_class = new Mail();

    if(isset($_GET['v']))
        {
            $mail_class->verify_mail();
        }
    elseif(isset($_POST['submit']))
        {
            extract($_POST);
            if($user->verify_token($token))
                {
                    echo $mail;
                    $mail_class->send_verification($mail);
                }
        }
    else
        {
            ?>
                <p>Missing the verification email? Enter your email here and we'll send it to you again. Remember to check your spam folder and double-check the email address you entered when you registered.</p>
                <form method='post'>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
                    Email: <br>
                    <input type='text' name='mail' placeholder='Your email address'><br><br>
                    <input type='submit' name='submit' value='Register' />
                </form>
            <?php
        }
?>