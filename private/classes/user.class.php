<?php
require_once dirname( __DIR__ ) . '/autoload.php';

class User extends Security
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
    //user registration. Called from register.php
    public function register_user($username,$mail,$password)
    {
        $sql="SELECT * FROM users WHERE UPPER(username) = ? OR mail = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss",$username,$mail);
        $stmt->execute();

        $result = $stmt->get_result();
        $num = $result->num_rows;
        $stmt->close();

        $password = password_hash($password, PASSWORD_DEFAULT);

        //if username/mail is available, create new user
        if ($num == 0)
        {
            $sql1="INSERT INTO users (username, mail, password) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql1);
            $stmt->bind_param("sss",$username,$mail,$password);
            $stmt->execute();

            $result = $stmt->get_result();
            $stmt->close();
            return true;
        }
        else
        {
            $stmt->close();
            return false;
        }

    }

    // logging in. Called from login.php and register.php
    public function check_login($username, $password)
    {
        $sql = "SELECT * FROM users WHERE UPPER(username) = UPPER(?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s",$username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
        $hash = $user_data['password'];
        $stmt->close();

        //login success
        if(password_verify($password, $hash))
        {
            //creating session variables
            session_regenerate_id();
            $_SESSION['login'] = true;
            $_SESSION['id'] = $user_data['id'];
            $_SESSION['username'] = $user_data['username'];
            return true;
        }
        else
        {
            // Login failed - wrong username/password or non-existing user.
            exit('Wrong username or password');
        }
    }

    //logging out the user by destroying the session. Called from logout.php
    public function user_logout()
    {
        session_destroy();
        header("location:/../index.php");
    }

    //Shows a very simple user profile. Called from profile.php
    public function show_profile($pid)
    {
        $sql = "SELECT username, joindate FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i",$pid);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        echo 'Username: ' . $this->specialchars($row['username']) . '<br> Joined: ' . $row['joindate'];
    }
}
?>