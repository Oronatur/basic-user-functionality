<?php
require_once dirname( __DIR__ ) .'/private/header.php';
$user = new user();

//check if the user is trying to view someone else's profile
if(isset($_GET['id']))
    {
        $pid = $user->specialchars($_GET['id']);
    }
//if not, they'll see their own profile instead.
elseif(isset($_SESSION['login']))
    {
        $pid = $_SESSION['id'];
    }
//or get redirected if they're not logged in.
elseif(!isset($_SESSION['login']))
    {
        header("location:login.php");
    }
else
    {
        exit('Unexpected error.');
    }

$user->show_profile($pid);

?>
