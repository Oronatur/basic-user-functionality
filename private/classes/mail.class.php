<?php
require_once dirname( __DIR__ ) . '/autoload.php';

class Mail extends Security
{
    public function __construct()
    {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

        if(mysqli_connect_errno())
        {
            echo "Error: Could not connect to database.";
            exit;
        }
    }
    //sending email verification. Called from register.php
    public function send_verification($mail)
    {
        $sql = "SELECT username FROM users WHERE UPPER(mail) = UPPER(?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s",$mail);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        $username = $this->specialchars($row['username']);

        //sending verification email. $verification is a unique key based on username.
        $verification = md5($username);
        $link = 'ADDRESS_TO_YOUR_SITE?v=' . $verification;

        $subject = 'Please confirm your email address.';
        $message = 'Click  ' . $link . '  to confirm your email address.';

        mail($mail, $subject, $message);
    }

    //verifying email. Called from verify.php
    public function verify_mail()
    {
        if (!isset($_SESSION['login']))
        {
            exit ('You have to be logged in to verify your email.');
        }

        //creating a verification key. It's using a md5 of the users username because it's simple and unique (since usernames are unique)
        $verification = md5($_SESSION['username']);

        $sql = "SELECT authenticated FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i",$_SESSION['id']);
        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();
        $preval = $row['authenticated'];
        $stmt->close();

        //is the user logged in, is the verification key right, or have they already authenticated their email once?
        if(isset($_SESSION['login']) && $_GET['v'] == $verification  && $preval == 1)
        {
            exit ('Email already authenticated!');
        }
        else if(isset($_SESSION['login']) && $_GET['v'] == $verification && $preval == 0)
        {
            $sql = "UPDATE users SET authenticated = 1 WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i",$_SESSION['id']);
            $stmt->execute();
            $stmt->close();

            echo 'Email authenticated!';
        }
        else
        {
            echo 'Something went wrong.';
        }
    }
}

?>