<?php
require 'bd.php';


$login = $_POST['Login'];
$pass =  $_POST['Pass'];

if (!isset($login) || !isset($pass))
{
	echo "error login-password";
	exit;
}

echo register_user($login, $pass)
?>