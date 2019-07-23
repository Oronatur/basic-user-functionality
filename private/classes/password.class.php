<?php
require_once dirname( __DIR__ ) . '/autoload.php';

class password extends user
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
    //setting the variables and sending the mail for resetting the password. Called from forgot_password.php
    public function reset_password($mail)
    {
        //getting user data
        $sql = "SELECT id FROM users WHERE mail = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s",$mail);
        $stmt->execute();

        $result = $stmt->get_result();
        $num = $result->num_rows;
        $stmt->close();

        //checking if user exists
        if($num != 0)
            {
                // Creating tokens
                $selector = bin2hex(random_bytes(8));
                $token = bin2hex(random_bytes(32));
                $validator = bin2hex($token);

                //using the url for my own testing website.
                $url = 'YOUR SITE HERE/public/forgot_password.php?selector='. $selector.'&validator='.$validator;

                // Token expiration, set to last for 1H
                $expires =  date("Y-m-d H:i:s", strtotime("+1 hours"));

                //deleting old records, if any exist
                $sql2 = "DELETE FROM password_reset WHERE mail = ?";
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->bind_param("s",$mail);
                $stmt2->execute();
                $stmt->close();

                // Insert reset token into database
                $sql3 = "INSERT INTO password_reset (mail, selector, token, expires) VALUES (?, ?, ?, ?)";
                $stmt3 = $this->db->prepare($sql3);
                $stmt3->bind_param("ssss",$mail,$selector,$token,$expires);
                $stmt3->execute();
                $stmt->close();

                //sending the mail
                $subject = 'Requested password reset.';
                $message = 'Click this link to reset your password: ' . $url;

                mail($mail, $subject, $message);

                echo 'If there is a user connected to the username or email you submitted, they will recieve a mail with a reset link.';
            }
        else
            {
                echo 'If there is a user connected to the username or email you submitted, they will recieve a mail with a reset link.';
            }
    }

    //exectutes the actual password change if all the variables add up. Called from forgot_password.php
    public function reset_password_do($selector,$validator,$newpassword)
    {
        //getting the relevant data from the password reset table
        $sql = "SELECT * FROM password_reset WHERE selector = ? AND expires >= NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s",$selector);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        //getting the tokens from the database
        $authentication_token = hex2bin($validator);
        $db_token = $row['token'];
        $mail = $row['mail'];

        // Validating tokens
        if ($authentication_token == $db_token)
        {
            $sql2 = "SELECT * FROM users WHERE mail = ?";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->bind_param("s",$mail);
            $stmt2->execute();
            $stmt2->close();

            //if query ran successfully, update password and delete row from password reset table.
            if($stmt2)
            {
                $password = password_hash($newpassword, PASSWORD_DEFAULT);

                $sql3 = "UPDATE users SET password = ? WHERE mail = ?";
                $stmt3 = $this->db->prepare($sql3);
                $stmt3->bind_param("ss",$password,$mail);
                $stmt3->execute();
                $stmt->close();

                $sql4 = "DELETE FROM password_reset WHERE mail = ?";
                $stmt4 = $this->db->prepare($sql4);
                $stmt4->bind_param("s",$mail);
                $stmt4->execute();
                $stmt->close();

                if($stmt3)
                {
                    session_destroy();
                    exit('Password updated successfully. <a href="login.php">Login here</a>');
                }
                else
                {
                    exit('Error.');
                }
            }
            else
            {
                exit('Error.');
            }
        }
        else
        {
            exit('Error.');
        }
    }

    //for setting a new password when you remember your old one. Don't confuse this with password reset, above. Called from change_password.php.
    public function set_new_password($npassword, $npassword2, $cpassword)
    {
        //selecting old password
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i",$_SESSION['id']);
        $stmt->execute();

        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
        $stmt->close();
        $password = $user_data['password'];

        //checking if user is logged in
        if ($_SESSION['login'] == false)
        {
            echo 'You need to be logged in to change your password. <a href="login.php"> Click here </a> to log in.';
            exit();
        }
        //do the new passwords match?
        elseif($npassword != $npassword2)
        {
            echo 'Passwords do not match';
            exit();
        }
        //is the new password long enough?
        elseif(strlen($npassword) < 7)
        {
            echo 'Password must be over 8 characters long';
            exit();
        }
        elseif($_SESSION['login'] == true && $npassword == $npassword2)
        {
            //does the user have the old password?
            if(password_verify($cpassword, $password))
            {
                $password = password_hash($npassword,PASSWORD_DEFAULT);
                $sql2 = "UPDATE users SET password = ? WHERE id = ?";
                $stmt2 = $this->db->prepare($sql2);
                $stmt2->bind_param("si",$password,$_SESSION['id']);
                $stmt2->execute();
                $stmt2->close();

                echo 'Password updated.';
                session_regenerate_id();
                return;
            }
            elseif($cpassword != $password)
            {
                echo 'Old password incorrect.';
                exit();
            }
        }
        else
        {
            echo 'Error';
        }
    }
}
?>