<?php
require_once dirname( __DIR__ ) .'/private/header.php';

//if user is not logged in, redirect them to the login page.
if (!isset($_SESSION['login']))
{
    header("location:login.php");
}

//let the user change their password if they want/need to.
echo '<a href="change_password.php"> Click here to change your password.</a>';
?>